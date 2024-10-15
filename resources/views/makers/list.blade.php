@extends('layouts.app')
 
<table>
    <thead>
        <tr>
            <th>#</th>
            <th class="search-field">Gyártó</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entities as $entity)
            <tr>
                <td id={{$entity->id}}>{{$entity->id}}</td>
                <td>{{$entity->name}}</td>
            </tr>
        @endforeach
    </tbody>
</table>