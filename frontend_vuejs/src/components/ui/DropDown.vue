<template>
    <div :class="menuClass" :style="menuStyles" ref="Dropdown">
        <div class="trigger" @click.stop="triggerClick"  @mouseleave="triggerMouseleave" @mouseenter="triggerMouseEnter">
            {{ dropdownConfig.label }}
            <div v-if="dropdownConfig.hasCaret" :class="caret"></div>
        </div>
        <transition
            name="fade"
        >
            <ul v-if="menuOpen" :class="dropdownListClass" ref="DropdownList"  @click.stop @mouseleave="menuMouseLeave" @mouseenter="menuMouseEnter">
                <li 
                    
                    v-for="(child, idx) in dropdownConfig.children"
                    :key="child.id"
                >
                    <drop-down
                        v-if="child.children && child.children.length"
                        :dropdownConfig="{...child}"
                        :styles="modifiedStyles"
                        :isRoot="false"
                        @closeAll="handleCloseAll"
                        
                    ></drop-down>
                    <div
                        v-else
                        class="list-item"
                        @click="listItemOnClick(child, idx)"
                    >
                        {{ child.label }}
                        <div v-if="dropdownConfig.isSelect && isSelected(idx)">{{ "&#10003;" }}</div>
                    </div>
                    
                </li>

            </ul>
        </transition>
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
    background-color: var(--background-color-closed);
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
    > .trigger{
        background-color: var(--background-color-open);
    }
    > .dropdown-list {
        box-shadow: 0 0 4px 1px var(--background-color-open);

    }
    .list-item:hover{
        
        background-color: var(--background-color-open);
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
    white-space: nowrap;
}

.dropdown-list {
    color: var(--text-color-1);
    padding: 0;
    position: absolute;
    list-style-type: none;
    z-index: 2;
    top: 100%;
    min-width: var(--dropdown-min-width);
    
    
}

.list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: var(--dropdown-padding);
    height: var(--dropdown-height);
    text-align: left;
    white-space: nowrap;
    background-color: var(--background-color-closed);
}
.list-item:hover {
    color: var(--text-color-3);
}


.position-left {
    top: 0;
    right: 100%;
}

.position-right{
    top: 0;
    left: 100%;
}


.position-over {
    top: -50%;

}

.position-below {
    
}


.fade-enter-from {
    opacity: 0;
}

.fade-enter-active {
    transition: all var(--time-to-open) ease-out;
}

.fade-enter-to {
    opacity: 1;
}

.fade-leave-from {
    opacity: 1;
}

.fade-leave-active {
    transition: all var(--time-to-open) ease-in;
}

.fade-leave-to {
    opacity: 0;
}





</style>



<script lang="ts">
import { defineComponent, PropType } from 'vue';

type timeoutIDs = {
  [key: string]: number;
};

export interface DropDownListNode {
    id: string,
    trigger?: TriggerMode
    opening?: OpeningMode,
    label: string,
    action?: string,
    children?: DropDownListNode[],
    isSelect?: boolean,
    hasCaret?: boolean,
    selectedItemIdx? : number,
    closeOnListItemClicked?: boolean
}

export enum OpeningMode {
    FADE_IN_BELOW = 0,
    FADE_IN_LEFT = 1,
    FADE_IN_RIGHT = 2,
    FADE_IN_OVER = 3,
}

export enum TriggerMode {
    HOVER = 0,
    CLICK = 1
}


interface DropDownData {
    menuOpen: boolean,
    overTrigger: boolean,
    inMenu: boolean,
    slideMenuClosed: boolean,
    submenuOpened: boolean,
    timeourIDs: timeoutIDs
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
            overTrigger: false,
            inMenu: false,
            slideMenuClosed: false,
            submenuOpened: false,
            timeourIDs: {}
        }
    },
    props: {
        dropdownConfig: {
            type: Object as PropType<DropDownListNode>,
            required: true
        },
        styles: {
            type: Object as PropType<DropDownStyles>,
            required: false
        },
        isRoot: {
            type: Boolean,
            required: false,
            default: true
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
            const backgroundColors = styles.backgroundColors ? styles.backgroundColors :['red', 'green', 'blue'];
            const backgroundColorClosed = backgroundColors[0];
            const backgroundColorOpen = backgroundColors[1];
            
            const timeToOpen = styles.timeToOpen ? styles.timeToOpen: 3;
            return {
                '--dropdown-padding': `${listItemPadding}`,
                '--dropdown-height': `${listItemHeight}`,
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
        dropdownListClass(): Record<string, boolean> {
            const classes: Record<string, boolean> = {
                'dropdown-list': true
            };
            switch (this.dropdownConfig.opening) {
                case OpeningMode.FADE_IN_BELOW:
                    classes['position-below'] = true;
                    break;
                case OpeningMode.FADE_IN_OVER:
                    classes['position-over'] = true;
                    break;
                case OpeningMode.FADE_IN_LEFT:
                    classes['position-left'] = true;
                    break;
                case OpeningMode.FADE_IN_RIGHT:
                    classes['position-right'] = true;
                    break;
                default:
                    classes['position-below'] = true;
                    break;
            }
            return classes;
        },
        modifiedStyles(): DropDownStyles {
            
            const stylesCopy: DropDownStyles = JSON.parse(JSON.stringify(this.$props.styles));
            let backgroundColors = stylesCopy?.backgroundColors;
            if (!backgroundColors) backgroundColors = ['green', 'blue', 'red'];
            stylesCopy.backgroundColors = backgroundColors;
            return stylesCopy;
        },
        getSubmenuHeight(): string {
            const menu = this.$refs.Dropdown as HTMLDivElement;
            const height = getComputedStyle(menu).getPropertyValue('--expand-list-height');
            return height;
        }
    },
    methods: {
        triggerClick() {
            clearTimeout(this.timeourIDs[this.dropdownConfig.id]);
            if (this.dropdownConfig.trigger !== TriggerMode.CLICK) return;
            if (this.menuOpen === false) {
                this.menuOpen = true;
              
                window.addEventListener('click', this.closeMenu);
            } else {
                this.menuOpen = false;
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
        listItemOnClick(item: DropDownListNode, idx: number) {
            if (this.dropdownConfig.closeOnListItemClicked) this.handleCloseAll();
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