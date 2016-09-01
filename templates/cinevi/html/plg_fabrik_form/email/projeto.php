<?php
defined('_JEXEC') or die('Restricted access');
/*<table border="0">
	<tr><td>Name</td><td><?php echo $this->data['custom_balloon_orders___full_name']?></td></tr>
	<tr><td>Phone</td><td><?php echo $this->data['custom_balloon_orders___phone']?></td></tr>
	<tr><td>Email</td><td><?php echo $this->data['custom_balloon_orders___email']?></td></tr>
	<tr><td>Shop</td><td><?php echo JArrayHelper::getValue($this->data['custom_balloon_orders___shop'], 0, '')?></td></tr>
	<tr><td>Custom Balloon Description</td>
	<td><?php echo JArrayHelper::getValue($this->data['custom_balloon_orders___custom_balloon_description'], 0, '')?></td></tr>
	<tr><td>ref</td><td><?php echo $this->data['custom_balloon_orders___id']?></td></tr>
</table>*/
?>

<h2 style="text-align: center;">Detalhes do Projeto {cine7_projetos___Nome} (#{cine7_projetos___id})</h2>

<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #045771; font-size: 14px;">
    <tr>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Id</td>
	<td colspan="2" style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Nome do Projeto</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Responsável</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Email</td>
    </tr>
    <tr>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___id}</td>
	<td colspan="2" style="padding: 6px 6px 10px;">{cine7_projetos___Nome}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Responsavel}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Email}</td>
    </tr>
    <tr>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Telefone</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Matrícula</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Modalidade</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Professor</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Formato</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Gênero</td>
    </tr>
    <tr>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Telefone}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Matricula}</td>
	<td style="padding: 6px 6px 10px;">cine7_projetos___Modalidade</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Professor}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Formato}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Genero}</td>
    </tr>
    <tr>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Local</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Pré-Produção</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Produção</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Pós-Produção</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Submissão</td>
    </tr>
    <tr>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Local}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___PreProducao }</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Producao }</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___PosProducao }</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Submissao}</td>
    </tr>
    <tr>
	<td colspan="2" style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Diretores</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Matricula</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Email</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Telefone</td>
    </tr>
    <tr>
	<td colspan="2" style="padding: 6px 0 10px;">{cine7_projetos___Diretores}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___MatriculaDiretores}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___EmailDiretores}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___TelefoneDiretores}</td>
    </tr>
    <tr>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Produtores</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Matricula</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Email</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Telefone</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Habilitação</td>
    </tr>
    <tr>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___Produtores}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___MatriculaProdutores}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___EmailProdutores}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___TelefoneProdutores}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___HabilitacaoProdutores}</td>
    </tr>
    <tr>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Arte</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Matricula</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Email</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Telefone</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Habilitação</td>
    </tr>
    <tr>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___DiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___MatriculaDiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___EmailDiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___TelefoneDiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___HabilitacaoDiretorFotografia}</td>
    </tr>
    <tr>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Som</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Matricula</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Email</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Telefone</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Habilitação</td>
    </tr>
    <tr>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___DiretorSom}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___MatriculaDiretorSom}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___EmailDiretorSom}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___TelefoneDiretorSom}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___HabilitacaoDiretorSom}</td>
    </tr>
    <tr>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Direção de Arte</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Matricula</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Email</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Telefone</td>
	<td style="padding: 6px; background-color: #045771; color: #FFF; font-weight: bold;">Habilitação</td>
    </tr>
    <tr>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___DiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___MatriculaDiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___EmailDiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___TelefoneDiretorArte}</td>
	<td style="padding: 6px 6px 10px;">{cine7_projetos___HabilitacaoDiretorArte}</td>
    </tr>
</table>
