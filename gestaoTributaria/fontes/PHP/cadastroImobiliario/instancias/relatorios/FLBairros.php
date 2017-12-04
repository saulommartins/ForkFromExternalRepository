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
    * Página de filtro para o relatório de bairros
    * Data de Criação   : 23/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: FLBairros.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.19
*/

/*
$Log$
Revision 1.9  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"  );

unset( $sessao->filtro );

$pgOcul = "OCBairros.php";
include_once 'JSBairros.js';

$obRCIMBairro    = new RCIMBairro;
Sessao::remove('sessao_transf5');

$obRCIMBairro->listarUF( $rsUF );

while ( !$rsUF->eof() ) {
    $sessao->nomFiltro['uf'][$rsUF->getCampo( 'cod_uf' )] = $rsUF->getCampo( 'nom_uf' );
    $rsUF->proximo();
}
$rsUF->setPrimeiroElemento();

//****************************************/

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
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCBairros.php" );

$obTxtNomBairro = new TextBox;
$obTxtNomBairro->setName           ( "stNomBairro"        );
$obTxtNomBairro->setId             ( "stNomBairro"        );
$obTxtNomBairro->setRotulo         ( "Nome do Bairro"     );
$obTxtNomBairro->setTitle          ( "" );
$obTxtNomBairro->setSize           ( "80" ) ;
$obTxtNomBairro->setMaxlength      ( "80" ) ;

$obCodInicio = new TextBox;
$obCodInicio->setName              ( "inCodInicio"        );
$obCodInicio->setRotulo            ( "Código do Bairro"   );
$obCodInicio->setTitle             ( "Informe um período" ) ;
$obCodInicio->setInteiro           ( true                );

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName             ( "inCodTermino"       );
$obCodTermino->setRotulo           ( "Código do Bairro"   );
$obCodTermino->setTitle            ( "Informe um período" );
$obCodTermino->setInteiro          ( true                 );

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"                 );
$obTxtCodUF->setName               ( "inCodigoUF"             );
$obTxtCodUF->setValue              ( $_REQUEST["inCodigoUF"]  );
$obTxtCodUF->setSize               ( 8                        );
$obTxtCodUF->setMaxLength          ( 8                        );
$obTxtCodUF->setNull               ( true                     );
$obTxtCodUF->setInteiro            ( true                     );
$obTxtCodUF->obEvento->setOnChange ( "buscaValor('buscaMunicipio');" );

$obCmbUF = new Select;
$obCmbUF->setName                  ( "inCodUF"                 );
$obCmbUF->addOption                ( "", "Selecione"           );
$obCmbUF->setCampoId               ( "cod_uf"                  );
$obCmbUF->setCampoDesc             ( "nom_uf"                  );
$obCmbUF->preencheCombo            ( $rsUF                     );
$obCmbUF->setValue                 ( $_REQUEST["inCodigoUF"]   );
$obCmbUF->setNull                  ( true                      );
$obCmbUF->setStyle                 ( "width: 220px"            );
$obCmbUF->obEvento->setOnChange    ( "buscaValor('preencheMunicipio');"  );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo      ( "Munic&iacute;pio"             );
$obTxtCodMunicipio->setName        ( "inCodigoMunicipio"            );
$obTxtCodMunicipio->setValue       ( $_REQUEST["inCodigoMunicipio"] );
$obTxtCodMunicipio->setSize        ( 8                              );
$obTxtCodMunicipio->setMaxLength   ( 8                              );
$obTxtCodMunicipio->setNull        ( true                           );
$obTxtCodMunicipio->setInteiro     ( true                           );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName           ( "inCodMunicipio"               );
$obCmbMunicipio->addOption         ( "", "Selecione"                );
$obCmbMunicipio->setCampoId        ( "cod_municipio"                );
$obCmbMunicipio->setCampoDesc      ( "nom_municipio"                );
$obCmbMunicipio->setValue          ( $_REQUEST["inCodigoMunicipio"] );
$obCmbMunicipio->setNull           ( true                           );
$obCmbMunicipio->setStyle          ( "width: 220px"                 );

$sessao->nomFiltro['ordenacao']['codigo']    = "Código do bairro";
$sessao->nomFiltro['ordenacao']['uf']        = "Estado";
$sessao->nomFiltro['ordenacao']['municipio'] = "Município";
$sessao->nomFiltro['ordenacao']['bairrro']   = "Bairro";

$obCmbOrder = new Select;
$obCmbOrder->setName               ( "stOrder"                 );
$obCmbOrder->setRotulo             ( "Ordenação"               );
$obCmbOrder->setTitle              ( "Escolha a ordenação do relatório" );
$obCmbOrder->addOption             ( "", "Selecione"           );
$obCmbOrder->addOption             ( "codigo"    , "Código"    );
$obCmbOrder->addOption             ( "uf"        , "Estado"    );
$obCmbOrder->addOption             ( "municipio" , "Município" );
$obCmbOrder->addOption             ( "bairrro"   , "Bairro"    );
$obCmbOrder->setCampoDesc          ( "stOrder"                 );
$obCmbOrder->setNull               ( false                     );
$obCmbOrder->setStyle              ( "width: 100px"            );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.19" );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente        ( $obTxtNomBairro );
$obFormulario->agrupaComponentes    ( array( $obCodInicio, $obLblPeriodo ,$obCodTermino) );
$obFormulario->addComponenteComposto( $obTxtCodUF, $obCmbUF               );
$obFormulario->addComponenteComposto( $obTxtCodMunicipio, $obCmbMunicipio );
$obFormulario->addComponente( $obCmbOrder );
$obFormulario->OK();
$obFormulario->setFormFocus( $obTxtNomBairro->getId() );
$obFormulario->show();
?>
