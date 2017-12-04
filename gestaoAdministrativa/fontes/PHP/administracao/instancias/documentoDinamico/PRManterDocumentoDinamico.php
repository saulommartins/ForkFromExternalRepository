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
* Arquivo de instância para manutenção de documentos dinâmicos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 5854 $
$Name$
$Author: lizandro $
$Date: 2006-02-01 16:38:53 -0200 (Qua, 01 Fev 2006) $

Casos de uso: uc-01.03.99
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GA_ADM_NEGOCIO."RDocumentoDinamicoDocumento.class.php" );
include_once (CAM_FW_BANCO_DADOS."Transacao.class.php"  );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumentoDinamico" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$obRDocumentoDinamico = new RDocumentoDinamicoDocumento;

switch ($stAcao) {
   case "alterar":
        for ($inCount = 1; $inCount <= $_REQUEST['inBloco']; $inCount++) {
            $stBloco = "stBloco".$inCount;
            $stAlinhamento = "boAlinhamento-".$inCount;

            $obRDocumentoDinamico->addDocumentoBlocoTexto();
            $obRDocumentoDinamico->roUltimoBlocoTexto->setTexto($_REQUEST[$stBloco]);
            $obRDocumentoDinamico->roUltimoBlocoTexto->setAlinhamento($_REQUEST[$stAlinhamento]);
        }

        $obRDocumentoDinamico->setCodDocumento                ( $_REQUEST["inCodDocumento"]  );
        $obRDocumentoDinamico->obRModulo->setCodModulo        ( $_REQUEST["inCodModulo"]        );
        $obRDocumentoDinamico->setMargem_esq                  ( $_REQUEST["inMargEsq"]     );
        $obRDocumentoDinamico->setNom_documento               ( $_REQUEST["stDocumento"]     );
        $obRDocumentoDinamico->setTitulo                      ( $_REQUEST["stTitulo"]     );
        $obRDocumentoDinamico->setMargem_dir                  ( $_REQUEST["inMargDir"]     );
        $obRDocumentoDinamico->setMargem_sup                  ( $_REQUEST["inMargSup"]     );
        $obRDocumentoDinamico->setTamanhoFonte                ( $_REQUEST["inTamFonte"]  );
        if ($_REQUEST['fonte'] == 0) {
           $obRDocumentoDinamico->setFonte                    ('T');
        } else {
           $obRDocumentoDinamico->setFonte                    ( $_REQUEST["fonte"]         );
        }
//        $obRDocumentoDinamico->setAlturaLinha                 ( $_REQUEST["inAlturaLinha"]   );

        $obErro = $obRDocumentoDinamico->alterarDocumento ();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Cargo: ".$_REQUEST['hdnModulo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

}
?>
