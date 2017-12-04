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
* Arquivo de implementação de manutenção de histórico de arquivamento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 29075 $
$Name$
$Author: rodrigosoares $
$Date: 2008-04-09 08:40:29 -0300 (Qua, 09 Abr 2008) $

Casos de uso: uc-01.06.92
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.92');

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

if (isset($_REQUEST["codHistorico"])) {
    $ctrl = 1;
}

if (isset($_REQUEST["pagina"])) {
    Sessao::write('pagina',$_REQUEST["pagina"]);
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
switch ($ctrl) {
case 0:
       if (isset($_REQUEST["acao"])) {
            Sessao::write('pagina','');
            $sql =  "select cod_historico, nom_historico from sw_historico_arquivamento";
            Sessao::write('sSQLs',$sql);
    }

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina(Sessao::read('pagina'));
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_historico","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec .= "
 <table width=100% id='processos'>
    <tr>
        <td colspan=4 class=alt_dados>Registros de Motivo do Arquivamento</td>
    </tr>
    <tr>
        <td class=labelleftcabecalho width=5%>&nbsp;</td>
    <td class=labelleftcabecalho width=12%>Código</td>
    <td class=labelleftcabecalho width=80%>Descrição</td>
    <td class=labelleftcabecalho >&nbsp;</td>
    </tr>
        ";
    $cont = 1;
        while (!$dbEmp->eof()) {
                $codHistorico = $dbEmp->pegaCampo("cod_historico");
                $nomHistorico = $dbEmp->pegaCampo("nom_historico");
                $dbEmp->vaiProximo();

                $exec .= "
            <tr>
            <td class=show_dados_center_bold>".$cont++."</td>
                <td class=show_dados>".$codHistorico."</td>
                <td class=show_dados>".$nomHistorico."</td>


                <td class=botao ><a href='#'
onClick=\"alertaQuestao('".CAM_PROTOCOLO."protocolo/historicoPadrao/excluiHistoricoArquivamento.php?".str_replace("&","*_*",Sessao::getId())."*_*codHistorico=".$codHistorico."*_*nomHistorico=".addslashes(urlencode($nomHistorico))."*_*stDescQuestao=".addslashes(urlencode($nomHistorico))."','sn_excluir','".Sessao::getId()."');\">
<img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a></td>
            </tr>";

        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
break;

case 1:
    include '../configProtocolo.class.php';
    $configura = new configProtocolo;
    $configura->setaVariaveis($_REQUEST["codHistorico"]);
    if ($configura->excluiHistoricoArquivamento()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["codHistorico"]);
            $audicao->insereAuditoria();
            echo '
            <script type="text/javascript">
                alertaAviso("Motivo do Arquivamento: '.addslashes(urlencode($_REQUEST["nomHistorico"])).'","excluir","aviso","'.Sessao::getId().'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';
        } else
            echo '
            <script type="text/javascript">
                alertaAviso("Motivo do Arquivamento: '.addslashes(urlencode($_REQUEST["nomHistorico"])).'","n_excluir","erro","'.Sessao::getId().'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';
    break;
}
include '../../../framework/include/rodape.inc.php';

?>
