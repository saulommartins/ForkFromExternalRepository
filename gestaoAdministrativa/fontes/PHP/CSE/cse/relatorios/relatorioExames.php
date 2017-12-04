<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Arquivo de instância para Relatórios
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.99
*/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"       );
  include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"   );
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"   ); //Classe para gerar paginação dos dado
  include_once (CAM_FW_LEGADO."botoesPdfLegado.class.php"   );

if (isset($pagina)) {
    $controle = 1;
}

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}

switch ($controle) {
//Formulário em HTML para entrada de dados
case 0:
?>
<script type="text/javascript">
    function validacao(cod)
    {
        var f = document.frm;
        f.target = 'oculto';
        f.controle.value = cod;
        f.submit();
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        var f = document.frm;
        f.ok.disabled = true;
        if (Valida()) {
            f.controle.value = 1;
            f.target = "telaPrincipal";
            f.action = "<?=$PHP_SELF;?>?<?=$sessao->id;?>&filtro=1";
            f.submit();
        } else {
            f.ok.disabled = false;
        }
    }

    function buscaCidadao()
    {
        var f = document.frm;
        f.target = 'oculto';
        f.controle.value = 4;
        f.submit();
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target='telaPrincipal'>
<input type='hidden' name='controle' value='1'>
<table width='100%'>
<tr><td class="alt_dados" colspan="2">Filtrar por</td></tr>
<tr>
    <td class='label' width='20%'>Cidadão</td>
    <td class='field' width='80%'>
        <input type='text' name='codCidadao' value="<?=$codCidadao;?>" size='5' maxlength='10' onKeyUp="return autoTab(this, 10, event);" onKeyPress="return(isValido(this, event, '0123456789'))" onChange="buscaCidadao();">
        <input type='text' name='nomCidadao' value="<?=$nomCidadao;?>" size='60' maxlength='200' readonly="" tabindex="1">
        <a href='javascript:procurarCidadao("frm","codCidadao","nomCidadao","<?=$sessao->id?>");'>
            <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="Procurar cidadão" width=22 height=22 border=0>
        </a>
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Classificação de Tratamento</td>
    <td class='field' width='80%'>
        <?php
            $combo = montaComboGenerico("codClassificacao", "cse.classificacao_tratamento", "cod_classificacao", "nom_classificacao", $codClassificacao,
                     "style='width: 200px;' onchange='validacao(3);' ",
                     "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Tipo de Tratamento</td>
    <td class='field' width='80%'>
        <select name='codTipo' style='width: 200px;' disabled>
            <option value='XXX' selected>Selecione</option>
        </select>
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Instituição</td>
    <td class='field' width='80%'>
        <?php
            $combo = montaComboGenerico("codInstituicao", "cse.instituicao_saude", "cod_instituicao", "nom_instituicao", $codInstituicao,
                     "style='width: 200px;' ", "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Data de Realização do Exame</td>
    <td class='field' width='80%'>
        <input type="text" name="dataExame" value="<?=$dataExame;?>" size='10' maxlength='10'
        readonly='' onDblClick="retornaData(this);">
        <a href="javascript:MostraCalendario('frm','dataExame','<?=$sessao->id;?>');"><img src='<?=CAM_FW_IMAGENS."calendario.gif";?>' border='0'></a>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <?php geraBotaoOk(); ?>
    </td>
</tr>
</table>
</form>
<?php
    break;

//Exibe o relatório e os botões de salvar imprimir
case 1:

//Grava uma query com os filtros em uma variável de sessão
if (isset($filtro)) {
    $sql = "";
    if (isset($codClassificacao) and $codClassificacao != 'XXX') {
        $sql .= " And et.cod_classificacao = '".$codClassificacao."' ";
    }
    if (isset($codTipo) and $codTipo != 'XXX') {
        $sql .= " And et.cod_tipo = '".$codTipo."' ";
    }
    if (isset($codInstituicao) and $codInstituicao != 'XXX') {
        $sql .= " And et.cod_instituicao = '".$codInstituicao."' ";
    }
    if (isset($dataExame) and strlen($dataExame)) {
        $dataExame = dataToSql($dataExame);
        $sql .= " And et.dt_realizacao = '".$dataExame."' ";
    }

    //Guarda os filtros na variável de sessão para ser usado no laço dos exames
    $sessao->transf[filtro] = $sql;

$subquery = "Select c.cod_cidadao
            From cse.prescricao_exame as et, cse.cidadao as c
            Where et.cod_cidadao = c.cod_cidadao ".$sessao->transf[filtro];
if (strlen($codCidadao)) {
    $subquery .= " And c.cod_cidadao = '".$codCidadao."' ";
}

$sessao->transf[sql] = "Select cod_cidadao, nom_cidadao From cse.cidadao
                        Where cod_cidadao IN (".$subquery.") ";

}

//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sessao->transf[sql],"15");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_cidadao)","ASC");
$sSQL = $paginacao->geraSQL();

//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
$conn->fechaBD();
$conn->vaiPrimeiro();

if($conn->numeroDeLinhas==0)
    exit("<br><b>Nenhum registro encontrado!</b>");

    //Mostra a opção de imprimir ou salvar o relatório
    $pdf = $sessao->transf[sql]." Order by lower(nom_cidadao) ASC; ";
    $pdf .= "Select et.dt_realizacao, ex.nom_exame, i.nom_instituicao
            From cse.prescricao_exame as et, cse.tipo_exame as ex,
            cse.instituicao_saude as i
            Where et.cod_exame = ex.cod_exame
            And et.cod_tipo = ex.cod_tratamento
            And et.cod_classificacao = ex.cod_classificacao
            And et.cod_instituicao = i.cod_instituicao
            And et.cod_cidadao = &cod_cidadao
            ".$sessao->transf[filtro]."
            Order by et.dt_realizacao ASC ";
    $sXML       = CAM_CSE.'cse/relatorios/relatorioExames.xml';
    $botoesPDF  = new botoesPdfLegado;
    $botoesPDF->imprimeBotoes($sXML,$pdf,'','');

$html = "<table width='95%'>";

while (!$conn->eof()) {
    $codCidadao = $conn->pegaCampo("cod_cidadao");
    $nomCidadao = $conn->pegaCampo("nom_cidadao");
    $conn->vaiProximo();
    $html .= "
        <tr>
            <td class='alt_dados' width='20%' nowrap>Código Cidadão</td>
            <td class='alt_dados' width='80%' colspan='2'>Nome Cidadão</td>
        </tr>";
    $html .= "
        <tr>
            <td class='show_dados'>".$codCidadao."</td>
            <td class='show_dados' colspan='2'>".$nomCidadao."</td>
        </tr>";

    $sql2 = "Select et.dt_realizacao, ex.nom_exame, i.nom_instituicao
            From cse.prescricao_exame as et, cse.tipo_exame as ex,
            cse.instituicao_saude as i
            Where et.cod_exame = ex.cod_exame
            And et.cod_tipo = ex.cod_tratamento
            And et.cod_classificacao = ex.cod_classificacao
            And et.cod_instituicao = i.cod_instituicao
            And et.cod_cidadao = '".$codCidadao."'
            ".$sessao->transf[filtro]."
            Order by et.dt_realizacao ASC ";

    $conn2 = new dataBaseLegado;
    $conn2->abreBD();
    $conn2->abreSelecao($sql2);
    $conn2->fechaBD();
    $conn2->vaiPrimeiro();
    if ($conn2->numeroDeLinhas > 0) {
        $html .= "
        <tr>
            <td class='labelleft' width='20%' nowrap>Data do Exame</td>
            <td class='labelleft' width='20%'>Nome do Exame</td>
            <td class='labelleft' width='20%'>Instituição</td>
        </tr>";
    }
    while (!$conn2->eof()) {
        $nomExame = $conn2->pegaCampo("nom_exame");
        $data = dataToBr($conn2->pegaCampo("dt_realizacao"));
        $nomInstituicao = $conn2->pegaCampo("nom_instituicao");
        $conn2->vaiProximo();
        $html .= "
        <tr>
            <td class='show_dados'>".$data."</td>
            <td class='show_dados'>".$nomExame."</td>
            <td class='show_dados'>".$nomInstituicao."</td>
        </tr>";
    }
    $conn2->limpaSelecao();

    //Espaço entre registros
    $html .= "<tr><td colspan='3' height='3'></td></tr>";

}
$conn->limpaSelecao();
$html .= "</table>";
echo $html;
?>
    <table width='450' align='center'><tr><td align='center'><font size='2'>
    <?php $paginacao->mostraLinks();  ?>
    </font></tr></td></table>
<?php
break;

//Busca o nome do cidadão
case 2:
    if ($codCidadao > 0) {
        if (!$nomCidadao = pegaDado("nom_cidadao","cse.cidadao","Where cod_cidadao = '".$codCidadao."' ")) {
            $nomCidadao = "Cidadão Inexistente";
        }
    } else {
        $nomCidadao = "";
    }
    $js .= 'f.nomCidadao.value = "'.$nomCidadao.'" ';
    break;

case 3:
    $js = "";
    $js .= "campo = f.codTipo; \n";
    $js .= "campo.disabled = false; \n";
    $js .= "limpaSelect(f.codTipo,1); \n";
    if ($codClassificacao != "XXX" or $codClassificacao > 0) {
        $js .= "campo.disabled = false;";
        $sql = "Select cod_tratamento, nom_tratamento
            From cse.tipo_tratamento
            Where cod_classificacao = ".$codClassificacao;
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        $cont = 1;
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_tratamento");
                $nom = $conn->pegaCampo("nom_tratamento");
                $js .= 'campo.options['.$cont.'] = new Option("'.$nom.'",'.$cod.'); ';
                $conn->vaiProximo();
                $cont++;
            }
        $conn->limpaSelecao();
    } else {
        $js .= "campo.disabled = true;";
    }
    break;
case 4:
    $js = "f.controle.value = 0; \n";
    if ($codCidadao > 0) {
        if (!$nomCidadao = pegaDado("nom_cidadao","cse.cidadao","Where cod_cidadao = '".$codCidadao."' ")) {
            $js = "alertaAviso('Cidadão inválido!(".$codCidadao.")','form','erro','".$sessao->id."')\n";
            $nomCidadao = "";
        }
    } else {
        $nomCidadao = "";
    }
    $js .= 'f.nomCidadao.value = "'.$nomCidadao.'" ';

    break;
}//Fim switch

SistemaLegado::executaFrameOculto($js);

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
