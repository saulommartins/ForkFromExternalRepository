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
    * Página de Frame Oculto de Emissao
    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCCobrancaJudicial.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.03
*/

/*
$Log$
Revision 1.1  2007/09/11 20:44:13  cercato
*** empty log message ***

Revision 1.12  2007/09/06 19:26:24  cercato
setando exercicio correto na tabela emissao_documento.

Revision 1.11  2007/08/30 14:13:22  cercato
correcao na rotina de inclusao na tabela de emissao de documentos.

Revision 1.10  2007/08/15 19:58:57  cercato
alteracao nos documentos.

Revision 1.9  2007/08/14 15:13:54  cercato
adicionando exercicio em funcao de alteracao na base de dados.

Revision 1.8  2007/07/02 19:11:41  cercato
alterando fonte para pegar o proximo valor para reemissao.

Revision 1.7  2007/06/25 20:15:37  cercato
colocando extensao do arquivo "odt".

Revision 1.6  2007/04/24 18:32:01  cercato
inserindo campo novo na tabela de documentos.

Revision 1.5  2007/03/26 15:26:51  cercato
adicionando consultas para novos documentos com sistema pos-agata.

Revision 1.4  2007/03/01 14:21:13  cercato
Bug #8540#

Revision 1.3  2007/02/27 19:54:14  cercato
alteracoes em funcao das modificacoes nas tabelas do banco de dados.

Revision 1.2  2006/10/03 09:56:37  cercato
adionada funcao para download dos documentos.

Revision 1.1  2006/09/29 10:51:55  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATEmissaoDocumento.class.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "PreencheCGM":
        if ($_GET["inCGM"]) {
            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $_GET["inCGM"] );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( $rsCGM->Eof() ) {
                $stJs = 'f.inCGM.value = "";';
                $stJs .= 'f.inCGM.focus();';
                $stJs .= 'd.getElementById("stCGM").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@CGM não encontrado. (".$_GET["inCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
            }
        } else {
            $stJs = 'd.getElementById("stCGM").innerHTML = "&nbsp;";';
        }

        echo $stJs;
        break;
}
