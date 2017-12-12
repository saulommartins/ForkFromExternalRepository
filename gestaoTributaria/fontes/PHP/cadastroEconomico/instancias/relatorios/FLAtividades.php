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
    * Página de Filtro para relatorico de Contas
    * Data de Criação   : 29/04/2005

    * @author Analista Fabio Bertoldi
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    * $Id: FLAtividades.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.15

*/

/*
$Log$
Revision 1.8  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php" );

Sessao::write( "sessao_transf5", "" );

$pgJs = "JSAtividades.js";
$pgOcul = "OCAtividadesVigencia.php";

include_once( $pgJs );
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CEM_INSTANCIAS."relatorios/OCAtividades.php" );

$obCmbOrder = new Select;
$obCmbOrder->setName      ( "stOrder"                            );
$obCmbOrder->setRotulo    ( "Ordenação"                          );
$obCmbOrder->setTitle     ( "Selecione a ordenação do relatório" );
$obCmbOrder->addOption    ( ""          , "Selecione"            );
$obCmbOrder->addOption    ( "codigo"    , "Código Atividade"     );
$obCmbOrder->addOption    ( "descricao" , "Descrição Atividade"  );
$obCmbOrder->setCampoDesc ( "stOrder"      );
$obCmbOrder->setNull      ( false          );
$obCmbOrder->setStyle     ( "width: 200px" );

$obRCEMNivelAtividade = new RCEMNivelAtividade;
$obRCEMNivelAtividade->recuperaVigenciaAtual ( $rsVigenciaAtual );
$stVigenciaAtual = $rsVigenciaAtual->getCampo( "cod_vigencia" );
$obRCEMNivelAtividade->setCodigoVigencia( $stVigenciaAtual );
$obRCEMNivelAtividade->recuperaUltimoNivel( $rsUltimoNivel );
$stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
$obRCEMNivelAtividade->setCodigoVigencia("");
$obRCEMNivelAtividade->listarVigencia( $rsListaVigencia );
if ($_REQUEST["inCodigoVigencia"]) {
    $inCount = 0;
    while (!$rsListaVigencia->eof()) {
        if ($rsListaVigencia->getCampo('cod_vigencia') == $_REQUEST["inCodVigencia"]) {
            $inLinhaVigencia = $inCount; // key($rsListaVigencia->arElementos);
        }

        $rsListaVigencia->proximo();
        $inCount++;
    }
}

$rsListaVigencia->setPrimeiroElemento();

$obCmbDataVigencia = new Select;
$obCmbDataVigencia->setRotulo    ( "Vigência"        );
$obCmbDataVigencia->addOption    ( "", "Selecione"   );
$obCmbDataVigencia->setCampoId   ( "cod_vigencia"    );
$obCmbDataVigencia->setCampoDesc ( "dt_inicio"       );
$obCmbDataVigencia->setStyle     ( "width:150px"     );
$obCmbDataVigencia->setNull      ( false             );
$obCmbDataVigencia->setName      ( "inCodVigencia"   );
$obCmbDataVigencia->setId        ( $stVigenciaAtual  );
$obCmbDataVigencia->setValue     ( $stVigenciaAtual  );
$obCmbDataVigencia->preencheCombo( $rsListaVigencia  );
$obCmbDataVigencia->obEvento->setOnChange ( "buscaValor('montaAtividade');" );

$obSpnAtividade = new Span;
$obSpnAtividade->setID("spnAtividade");
//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda("UC-05.02.15");
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente ( $obCmbDataVigencia );
$obFormulario->addSpan( $obSpnAtividade );
$obFormulario->addComponente( $obCmbOrder );
$obFormulario->OK();
$obFormulario->show();

SistemaLegado::executaFrameOculto( "buscaValor('montaAtividade');" );
?>
