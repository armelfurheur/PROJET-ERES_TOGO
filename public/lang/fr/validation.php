<?php

return [

    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'max' => [
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'file' => 'Le fichier :attribute ne doit pas dépasser :max Ko.',
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max éléments.',
    ],
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'file' => 'Le fichier :attribute doit faire au moins :min Ko.',
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
    ],
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'unique' => 'Cette valeur pour :attribute est déjà utilisée.',
    'image' => 'Le fichier :attribute doit être une image.',
    'mimes' => 'Le fichier :attribute doit être de type :values.',
    'uploaded' => 'Le fichier :attribute n’a pas pu être téléchargé.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    
    'attributes' => [
        'nom' => 'nom',
        'email' => 'adresse email',
        'description' => 'description',
        'preuve' => 'preuve',
        'departement' => 'département',
        'localisation' => 'localisation',
        'gravity' => 'niveau de gravité',
    ],

];
