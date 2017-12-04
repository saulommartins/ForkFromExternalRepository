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
* Arquivo de implementação de manutenção de documento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24721 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:58:43 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.96
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.96');

if (!(isset($_REQUEST["controle"]))) {
    $controle = 0;
} else {
    $controle = $_REQUEST["controle"];
}
?>
 <script type="text/javascript">
 function zebra(id, classe)
 {
       var tabela = document.getElementById(id);
        var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
            ((i%2) != 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>
<?php
switch ($controle) {
case 0:

    if (isset($_REQUEST["acao"])) {

            $sSQLs = "SELECT cod_documento, nom_documento FROM sw_documento";
            Sessao::write('sSQLs',$sSQLs);
    }
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($_REQUEST["pagina"]);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_documento)","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "<table width='100%' id='processos'>
                    <tr>
                        <td class=alt_dados colspan=4>
                            Registros de documento
                        </td>
                    </tr>
                    <tr>
                        <td class=labelcenterCabecalho width='5%'>
                            &nbsp;
                        </td>
                        <td class=labelcenterCabecalho width='10%'>
                            Código
                        </td>
                        <td class=labelcenterCabecalho>
                            Descrição
                        </td>
                        <td class=labelcenterCabecalho>
                            &nbsp;
                        </td>
                    </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $codDocumentof  = trim($dbEmp->pegaCampo("cod_documento"));
                $nomDocumentof  = trim($dbEmp->pegaCampo("nom_documento"));
                $dbEmp->vaiProximo();
                $exec .= "<tr><td class=show_dados_center_bold>".$count++."</td>
                        <td class=show_dados_right>".$codDocumentof."</td>
                        <td class=show_dados>".$nomDocumentof."</td>
                <td class=botao width=20 title='Editar'><a href='alteraDocumento.php?".Sessao::getId()."&codDocumento=".$codDocumentof."&controle=1&pagina=".$pagina."'>
                <img src='".CAM_FW_IMAGENS."btneditar.gif' border=0></a></td></tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
            $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
break;
case 1:

$sSQL = "SELECT * FROM sw_documento WHERE cod_documento = ".$_REQUEST["codDocumento"];
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $codDocumentof  = trim($dbEmp->pegaCampo("cod_documento"));
        $nomDocumentof  = trim($dbEmp->pegaCampo("nom_documento"));
        $nomDocumentof = stripslashes($nomDocumentof);
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
?>
<script type="text/javascript">

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = trim( document.frm.nomDocumento.value );
        if (campo == "") {
            mensagem += "@O campo Descrição é obrigatório";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);
    }
    function Salvar()
    {
        if (Valida()) {
            document.frm.submit();
        }
    }
    function Cancela()
    {
        document.frm.action = "alteraDocumento.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&controle=0";
        document.frm.submit();
    }
</script>
<form name="frm" action="alteraDocumento.php?<?=Sessao::getId()?>&controle=2" method="POST" onsubmit='return Valida();'>

    <table width="100%">
        <tr>
            <td class=alt_dados colspan=2>
                Dados para documento
            </td>
        </tr>

        <tr>
            <td class=label title="Descrição do documento">
                *Descrição
            </td>
            <td class=field>
                <input type="text" name="nomDocumento" size=30 maxlength=60 value="<?=$nomDocumentof;?>">
                <input type="hidden" name="codDocumento" value="<?=$codDocumentof;?>">
                <input type="hidden" name="pagina" value="<?=$pagina?>">
            </td>
        </tr>

        <tr>
            <td class=field colspan=2>
                <?=geraBotaoAltera();?>
        </tr>

    </table>

</form>
<?php
break;
case 2:
include '../documentos.class.php';
$documento = new documentos;
$nomDocumento = addslashes($_REQUEST["nomDocumento"]);
$documento->setaVariaveis($_REQUEST["codDocumento"],$nomDocumento);

    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_documento", $nomDocumento, "sw_documento","And cod_documento <> '".$_REQUEST["codDocumento"]."'",1)) {
        alertaAviso("alteraDocumento.php?".Sessao::getId()."&controle=1&codDocumento=".$codDocumento,"O documento ".$nomDocumento." já existe!","unica","erro", ".Sessao::getId().");
    } else {
        if ($documento->updateDocumento()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomDocumento);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
                 alertaAviso("'.$nomDocumento.'","alterar","aviso", "'.Sessao::getId().'");
                 window.location = "alteraDocumento.php?'.Sessao::getId().'&pagina='.$_REQUEST["pagina"].'";
                 </script>';
        } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$nomDocumento.'","n_alterar","aviso", "'.Sessao::getId().'");
                 window.location = "alteraDocumento.php?'.Sessao::getId().'&pagina='.$_REQUEST["pagina"].'";
                 </script>';
        }
    }
break;

}
include '../../../framework/include/rodape.inc.php';
?>
