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
    * Arquivo que lista todas as manutenções para serem excluidas
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.10  2006/07/21 11:36:02  fernando
Inclusão do  Ajuda.

Revision 1.9  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../bens.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.07");
$mostra = new bens;
$exclui = new bens;

$mostra->codigo = $cod;

if (!(isset($ctrl))) {
    $ctrl=0;
}

if (isset($chave)) {
    $ctrl=2;
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php

switch ($ctrl) {

    // pesquisa e exibicao de BENS
    case 0:
        include_once '../bens/listarBens.php';
    break;

    // lista Manutencoes encontradas para o BEM selecionado
    case 1:
        $mostra->codigo = $codkey;
        $Mlista = $mostra->listaManutencao();
?>
        <table width="100%">
        <tr>
            <td colspan="3" class="alt_dados">Manutenção de Bens</td>
        </tr>
        <tr>
            <td class="label" width="5%">&nbsp;</td>
            <td class="labelcenter" >Data do Agendamento</td>
            <td class="label" width="5%">&nbsp;</td>
        </tr>
<?php
        if ($Mlista!="") {
            $cont = 1;

            while (list ($key, $val) = each ($Mlista)) {
                $chave = $codkey."/".$val;
                $dt = dataToBr($val);
?>
            <tr>
                <td class="labelcenter" width="5%"><?=$cont++;?></td>
                <td class="show_dados" width="100%"><?=$dt;?></td>
                <td class="botao">
                    <a href='#' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/manutencao/excluiManutencao.php?<?=Sessao::getId();?>','chave','<?=$chave;?>','manutenção de <?=$dt;?>','sn_excluir','<?=Sessao::getId();?>');">
                    <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif' title="Excluir" border='0'>
                    </a>
                </td>
<?php
            }
        } else {
?>
            <tr>
                <td class="show_dados_center" width="100%" colspan="3">Nenhuma manutenção encontrada para este bem.</td>
            </tr>
<?php
        }
?>
        </table>
<?php
    break;

    // executa operacao de exclusao de Manutencao de BEM no BD
    case 2:
        $variaveis             = explode( "/" , $chave );
        $exclui->codigo        = $variaveis[0];
        $exclui->dtAgendamento = $variaveis[1];
        if ( $exclui->excluiManutencao() ) {
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $exclui->codigo); //registra os passos no auditoria
            $audicao->insereAuditoria();
?>
            <script type="text/javascript">
                alertaAviso      ( "Bem: <?=$variaveis[0];?>" , "excluir" , "aviso" , "<?=Sessao::getId();?>"     );
                mudaTelaPrincipal( "excluiManutencao.php?<?=Sessao::getId();?>&ctrl=1&codkey=<?=$variaveis[0];?>" );
            </script>
<?php
        } else {
?>
            <script type="text/javascript">
                alertaAviso( "Manutenção" , "n_excluir" , "erro" , "<?=Sessao::getId();?>" );
            </script>
<?php
        }
    break;
}

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
