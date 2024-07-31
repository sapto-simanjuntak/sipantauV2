
<tr>
    <th>Status</th>
    <td>{{ $obj->status }}</td>
</tr>
<tr>
    <th>Created at</th>
    <td>{{ date_format($obj->created_at,"d/m/Y H:i:s") }}</td>
</tr>
<tr>
    <th>Modified at</th>
    <td>{{ date_format($obj->updated_at,"d/m/Y H:i:s") }}</td>
</tr>
<tr>
    <th>Deleted at</th>
    <td>{{ $obj->deleted_at }}</td>
</tr>
<tr>
    <th>Created by</th>
    <td>{{ optional($obj->createdByUser)->first_name }}</td>
</tr>
<tr>
    <th>Modified by</th>
    <td>{{ optional($obj->updatedByUser)->first_name }}</td>
</tr>
<tr>
    <th>Deleted by</th>
    <td>{{ optional($obj->deletedByUser)->first_name }}</td>
</tr>
