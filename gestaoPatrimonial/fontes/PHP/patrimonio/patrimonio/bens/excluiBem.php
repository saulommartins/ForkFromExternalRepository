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
    * Seleciona e exclui bens
    * Data de Criação   : 25/03/2003

    * @author Desenvolvedor Ricardo Lopes de Alencar
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.14  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.13  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.12  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php'; //Inclui classe para inserir auditoria
include_once '../bens.class.php'; //Inclui classe que controla os bens
include_once 'interfaceBens.class.php'; //Inclui classe que contém a interface html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.06");
if (isset($codBemEx)) {
    $controle = 1;
    $arBem = explode('-',$codBemEx);
    $codBem = $arBem[0];
    $descricao = $arBem[1];
}

if (!isset($controle)) {
    $controle = 0;
}

switch ($controle) {

    case 0:
        include_once 'listarBens.php';
    break;

    case 1:
        $bens = new bens;
        if ($bens->excluirBem($codBem)) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codBem);
            $audicao->insereAuditoria();

            //Exibe mensagem e redireciona
            alertaAviso($PHP_SELF."?ctrl_frm=2&pagina=".$_GET['pagina'],"Bem: $codBem"."-".$descricao,"excluir","aviso");

        } else {
            alertaAviso($PHP_SELF."?ctrl_frm=2&pagina=".$_GET['pagina'],"Bem: $codBem"."-".$descricao,"n_excluir","erro");
        }
    break;

}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
?>
