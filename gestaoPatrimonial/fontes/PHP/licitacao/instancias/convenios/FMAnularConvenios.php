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
    * Anulação de Convenio
    * Data de Criação   : 03/10/2006

    * @author Analista:
    * @author Desenvolvedor:  Lucas Teixeira Stephanou
    * @ignore

    $Revision: 16561 $
    $Name$
    $Autor: $
    $Date: 2006-10-09 09:18:10 -0300 (Seg, 09 Out 2006) $

    *Casos de uso: uc-03.05.14
*/

/*
$Log$
Revision 1.1  2006/10/09 12:18:10  domluc
Caso de Uso : uc-03.05.14

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "anular";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenios";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';

// limpar sessao de veiculos
Sessao::remove('boAlteracao');
Sessao::remove('nuValorAtual');
Sessao::remove('nuPercentualAtual');
Sessao::remove('rsVeiculos');
Sessao::remove('participantes');

$boAlterar = true;
require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoConvenio.class.php') ;
$obConvenio = new TLicitacaoConvenio;

$inNumConvenio = $_REQUEST[ 'inNumConvenio' ];
$stFiltro = ' AND  convenio.num_convenio = ' . $inNumConvenio . '' ;

$obConvenio->recuperaRelacionamento ( $rsConvenio , $stFiltro , ' convenio.num_convenio');

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( 'oculto');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnConvenio =  new Hidden;
$obHdnConvenio->setName   ( "inNumConvenio" );
$obHdnConvenio->setValue  ( $inNumConvenio  );

$obLblNumConvenio = new Label;
$obLblNumConvenio->setRotulo ( 'Número do Convênio' );
$obLblNumConvenio->setValue  ( $rsConvenio->getCampo( 'num_convenio' ) );

$obLblTipoConvenio = new Label;
$obLblTipoConvenio->setRotulo ( 'Tipo de Convênio' );
$obLblTipoConvenio->setValue  (  $rsConvenio->getCampo( 'descricao_tipo' ) );

$obLblObjeto = new Label;
$obLblObjeto->setRotulo ( 'Objeto' );
$obLblObjeto->setValue  ( $rsConvenio->getCampo( 'descricao_objeto' ) );

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setName   ( "stJustificativa"                       );
$obTxtJustificativa->setId     ( "stJustificativa"                       );
$obTxtJustificativa->setValue  ( $stJustificativa                        );
$obTxtJustificativa->setRotulo ( "Justificativa"                        );
$obTxtJustificativa->setTitle  ( "Informe a justificativa de anulação" );
$obTxtJustificativa->setNull   ( false                                );
$obTxtJustificativa->setRows   ( 3                                    );
$obTxtJustificativa->setCols   ( 64                                   );

$obDtAnulacao = new Data;
$obDtAnulacao->setName    ( 'dtAnulacao' );
$obDtAnulacao->setId      ( 'dtAnulacao' );
$obDtAnulacao->setRotulo  ( 'Data de Anulação' );
$obDtAnulacao->setTitle   ( 'Informe a Data de Anulação' );
$obDtAnulacao->setValue   ( date( 'd/m/Y' ) );
$obDtAnulacao->setNull    ( false );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda     ( "UC-03.05.14" );
$obFormulario->addTitulo    ( "Consulta de Convênio");
$obFormulario->addHidden    ( $obHdnCtrl        );
$obFormulario->addHidden    ( $obHdnAcao        );
$obFormulario->addHidden    ( $obHdnConvenio    );
$obFormulario->addComponente( $obLblNumConvenio );
$obFormulario->addComponente( $obLblTipoConvenio);
$obFormulario->addComponente( $obLblObjeto      );
$obFormulario->addComponente( $obTxtJustificativa );
$obFormulario->addComponente( $obDtAnulacao     );
$obFormulario->Ok();
$obFormulario->show();
