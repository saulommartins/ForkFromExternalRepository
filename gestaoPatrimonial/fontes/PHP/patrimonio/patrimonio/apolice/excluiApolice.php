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
    * Arquivo que exclui as apólices
    * Data de Criação   : 25/03/2003

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 17886 $
    $Name$
    $Autor: $
    $Date: 2006-11-20 10:36:37 -0200 (Seg, 20 Nov 2006) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.12  2006/11/20 12:36:37  bruce
Bug #6931#

Revision 1.11  2006/10/12 16:42:05  larocca
Bug #6931#

Revision 1.10  2006/07/21 11:34:42  fernando
Inclusão do  Ajuda.

Revision 1.9  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:27  diego

*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include '../apolice.class.php';
//setAjuda("UC-03.01.08");
    $pagina = $sessao->transf2;
    $exclui = new apolice;
    // exclui apolice do BD
    if ($exclui->excluiApolice($codigo)) {

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
        $audicao = new auditoriaLegada;;
        $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codigo);
        $audicao->insereAuditoria();

        echo '
            <script type="text/javascript">
                alertaAviso("Apólice '.$codigo.' - '.$sessao->seguradora.'","excluir","aviso", "'.Sessao::getId().'");
                mudaTelaPrincipal("listaApolice.php?ctrl=1&pagina='.$pagina.'&'.Sessao::getId().'&acao=74");
            </script>';

    // caso ocorra erro na exclusao, exibe msg de erro
    } else {
        echo '
            <script type="text/javascript">
                alertaAviso("Apólice '.$codigo.' - '.$sessao->seguradora. ' está sendo utilizada.' .   ' ","n_excluir","erro", "'.Sessao::getId().'");
                mudaTelaPrincipal("listaApolice.php?'.Sessao::getId().'&acao=74");
            </script>';
    }

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';?>
