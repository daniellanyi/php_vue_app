

export enum StorageType {
    LOCAL = 'Local',
    SESSION = 'Session'
}


export class StorageService {

    public static getStorage(storageType: StorageType) {
        let storage;
        switch (storageType) {
            case StorageType.SESSION:
                storage = sessionStorage;
                break
            case StorageType.LOCAL:
                storage = localStorage;
                break
            default:
                storage = localStorage;
        }
        return storage;
    }

    public static saveUserSettings(key: string, value: any, storageType: StorageType) {
        let userSettings;
        let storage;
        try {   
            storage = this.getStorage(storageType)
            userSettings = storage.getItem('userSettings');
            if (userSettings) {
                const userSettingsJSON = JSON.parse(userSettings);
                userSettingsJSON[key] = value;
                storage.setItem('userSettings', JSON.stringify(userSettingsJSON));
            } else {
                const newSave = {
                    [key] : value
                }
                storage.setItem('userSettings', JSON.stringify(newSave));
            }
        } catch (error) {
            console.log(`Error setting ${key} in ${storageType} Storage`);
        }
    }

    public static getUserSettings(key: string, storageType: StorageType) {
        let userSettings;
        let storage;
        try {
            storage = this.getStorage(storageType)
            userSettings = storage.getItem('userSettings');
            if (!userSettings) {
                throw new Error('userSettings not found');
            }
            const userSettingsJSON = JSON.parse(userSettings);
            return userSettingsJSON[key];
            } catch (error) {
                console.log(`Error retrieving ${key} from ${storageType} Storage`);
            }
            
        }
    
}