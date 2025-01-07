<template>
    <div :class="menuClass" :style="menuStyles" ref="Dropdown">
        <div class="trigger" @click.stop="triggerClick"  >
            {{ dropdownConfig.label }}
            <div v-if="dropdownConfig.hasCaret" :class="caret"></div>
        </div>
        <ul class="dropdown-list" ref="DropdownList"  @click.stop @mouseleave="menuMouseLeave" @mouseenter="menuMouseEnter">
            <li 
                
                v-for="(child, idx) in dropdownConfig.children"
                :key="child.id"
            >
                <div
                    class="list-item"
                    @click="listItemOnClick(child, idx)"
                >
                    {{ child.label }}
                    <div v-if="dropdownConfig.isSelect && isSelected(idx)">{{ "&#10003;" }}</div>
                </div>
                
            </li>

        </ul>
    </div>

</template>



<style lang="scss" scoped>



.caret {
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid var(--text-color-1);
}


.caret-rotate {
    transform: rotate(180deg);
}



.menu {
    position: relative;
    user-select: none;
    color: var(--text-color-1);
    transition: background-color var(--time-to-open) ease-out;
    > .trigger:hover {
        color: var(--text-color-3);
        
        
        .caret {
            border-top: 6px solid var(--text-color-3);
        }
    }

}

.menu-open > .trigger {
    color: var(--text-color-3);
    
    .caret {
        border-top: 6px solid var(--text-color-3);
    }
}

.menu-open {
    background-color: var(--background-color-open);
    > .dropdown-list{
        height: var(--expand-list-height);
        
    }
}



.trigger {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: var(--caret-gap, 10px);
    justify-content: space-between;
    padding: var(--dropdown-padding);
    height: var(--dropdown-height);
    min-width: var(--dropdown-min-width);
    text-align: left;
}

.dropdown-list {
    color: var(--text-color-1);
    padding: 0;
    position: absolute;
    list-style-type: none;
    z-index: 2;
    top: 100%;
    min-width: var(--dropdown-min-width);
    position: relative;
    overflow: hidden;
    height: 0px;
    transition: height var(--time-to-open) ease-out;
    border-top: 1px solid var(--border-color-1);
    border-bottom: 1px solid var(--border-color-1);
}

.list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: var(--dropdown-padding);
    height: var(--dropdown-height);
    text-align: left;
}
.list-item:hover {
    color: var(--text-color-3);
}




.slide-menu-closed {
  
    .menu {
        background-color: unset;
    }    
    
}

</style>



<script lang="ts">
import { defineComponent, PropType } from 'vue';



export interface DropDownListMenu {
    id: string,
    label: string,
    trigger: TriggerMode
    children?: DropListItem[],
    hasCaret?: boolean,
    selectedItemIdx? : number,
    closeOnListItemClicked?: boolean
    isSelect?: boolean,
    action?: string,
}


export interface DropListItem {
    id: string,
    label: string,
    action?: string,
    
}

export enum TriggerMode {
    HOVER = 0,
    CLICK = 1
}


interface DropDownData {
    menuOpen: boolean,
    expandListHeight: string,
    listItemsDisabled: boolean,
    overTrigger: boolean,
    inMenu: boolean,
}

interface DropDownStyles {
    listItemPadding?: string,
    listItemHeight?: string,
    listItemMinWidth?: string,
    backgroundColors?: [string, string, string],
    timeToOpen?: number,
}

export default defineComponent({
    data(): DropDownData {
        return {
            menuOpen: false,
            expandListHeight: '',
            listItemsDisabled: true,
            overTrigger: false,
            inMenu: false,
        }
    },
    props: {
        dropdownConfig: {
            type: Object as PropType<DropDownListMenu>,
            required: true
        },
        styles: {
            type: Object as PropType<DropDownStyles>,
            required: false
        },
     
    },
    computed: {
        
        menuStyles(): Record<string, string> {
            const styles = this.$props.styles;
            if (!styles) return {};
            const numberOfChildren = this.dropdownConfig.children?.length;
            if (!numberOfChildren) return {};
            const listItemPadding = styles.listItemPadding ? styles.listItemPadding : '0.5em';
            const listItemHeight = styles.listItemHeight ? styles.listItemHeight : '0.5em';
            const listItemMinWidth = styles.listItemMinWidth ? styles.listItemMinWidth : 'fit-content';
            const height = this.expandListHeight ? this.expandListHeight : `calc(${numberOfChildren} * (${getTotalVerticalPadding(listItemPadding)} + ${listItemHeight}))`;
            const backgroundColors = styles.backgroundColors ? styles.backgroundColors :['red', 'green', 'blue'];
            const backgroundColorClosed = backgroundColors[0];
            const backgroundColorOpen = backgroundColors[1];
            
            const timeToOpen = styles.timeToOpen ? styles.timeToOpen: 3;
            return {
                '--dropdown-padding': `${listItemPadding}`,
                '--dropdown-height': `${listItemHeight}`,
                '--expand-list-height': `${height}`,
                '--dropdown-min-width': `${listItemMinWidth}`,
                '--background-color-closed': backgroundColorClosed,
                '--background-color-open': backgroundColorOpen,
                '--time-to-open': `${timeToOpen}s`
            }
        },
        caret() : Record<string, boolean> {
            return {
                'caret': true,
                'caret-rotate': this.menuOpen
            }
        },
        menuClass(): Record<string, boolean> {
            const classes: Record<string, boolean> = {
                'menu' : true,
                'menu-open': this.menuOpen,
            };
            return classes;
        },
    },
    methods: {
        triggerClick() {
            if (this.dropdownConfig.trigger !== TriggerMode.CLICK) return;
            if (this.menuOpen === false) {
                this.menuOpen = true;
                const timeToOpen = this.$props.styles?.timeToOpen ? this.$props.styles?.timeToOpen : 3;
                setTimeout(()=> {
                    this.listItemsDisabled = false;
                   
                }, timeToOpen * 1000);
                window.addEventListener('click', this.closeMenu);
            } else {
                this.menuOpen = false;
                this.listItemsDisabled = true;
            }
        },
        triggerMouseMove() {
            if (this.dropdownConfig.trigger !== TriggerMode.HOVER) return;
            if (this.menuOpen === false) {
                this.menuOpen = true;
                this.overTrigger = true;
                window.addEventListener('mouseenter', this.closeMenu);
            }
        },
        triggerMouseleave() {
            if (this.dropdownConfig.trigger !== TriggerMode.HOVER) return;
            if (this.inMenu === true) return;
            this.overTrigger = false;
            this.closeMenu();
        },
        triggerMouseEnter() {
            if (this.dropdownConfig.trigger !== TriggerMode.HOVER) return;
            this.overTrigger = true;
            this.menuOpen = true;
        },
        
        menuMouseLeave() {
            if (this.dropdownConfig.trigger !== TriggerMode.HOVER) return;
            this.inMenu = false;
            this.closeMenu();
        },
        menuMouseEnter() {
            this.inMenu = true;
            this.overTrigger = false;
        },
        closeMenu() {
            setTimeout(()=>{
                if (this.dropdownConfig.trigger !== TriggerMode.HOVER) {
                    this.menuOpen = false;
                    
                }
                else if (!this.inMenu && !this.overTrigger) this.menuOpen = false;
                
            }, 0)
        },
        listItemOnClick(item: DropListItem, idx: number) {
            if (this.dropdownConfig.closeOnListItemClicked) this.handleCloseAll();
            if (this.listItemsDisabled === true) return;
            if (this.dropdownConfig.isSelect) {
                if (this.dropdownConfig.action) {
                    this.$store.dispatch(this.dropdownConfig.action, idx);
                }
            }
            if (!item.action) return;
            this.$store.dispatch(item.action, item.id);
        },
        isSelected(idx: number): boolean {
            return this.dropdownConfig.selectedItemIdx === idx;
        },
        handleCloseAll() {
            this.$emit('closeAll');
            this.menuOpen = false;
        },
        
        
       
    },
    emits: {
        'closeAll': function() :boolean {
            return true;
        },
    },
    beforeUnmount() {
        window.removeEventListener('click', this.closeMenu);
        window.removeEventListener('mouseenter', this.closeMenu);
    },

}) 

function getTotalVerticalPadding(paddingString: string) {
    const values = paddingString.trim().split(/\s+/);

    let top, bottom;

    switch (values.length) {
        case 1:
            top = bottom = values[0];
            break;
        case 2:
            top = bottom = values[0];
            break;
        case 3:
            top = values[0];
            bottom = values[2];
            break;
        case 4:
            top = values[0];
            bottom = values[2];
            break;
        default:
            throw new Error("Invalid padding shorthand string");
    }

    return `calc(${top} + ${bottom})`;
}


</script>