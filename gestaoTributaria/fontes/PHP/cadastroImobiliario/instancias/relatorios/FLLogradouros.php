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
    * Página de filtro para o relatório de logradouros
    * Data de Criação   : 28/03/2004

    * @author Analista: Fábio Bertoldi Rodigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: FLLogradouros.php 63656 2015-09-24 19:44:19Z evandro $

    * Casos de uso: uc-05.01.20
*/

/*
$Log$
Revision 1.7  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"  );

if (isset($sessao->filtro)) {
    unset( $sessao->filtro );
}

$pgOcul = "OCLogradouros.php";
include_once 'JSLogradouros.js';

$obRCIMLogradouro    = new RCIMLogradouro;
Sessao::remove('sessao_transf5');
$obRCIMLogradouro->listarTipoLogradouro( $rsTipoLogradouro );
while ( !$rsTipoLogradouro->eof() ) {
    $sessao->nomFiltro['tipo_logradouro'][$rsTipoLogradouro->getCampo( 'cod_tipo' )] = $rsTipoLogradouro->getCampo( 'nom_tipo' );
    $rsTipoLogradouro->proximo();
}
$rsTipoLogradouro->setPrimeiroElemento();

$obRCIMLogradouro->listarUF( $rsUF );
while ( !$rsUF->eof() ) {
    $sessao->nomFiltro['uf'][$rsUF->getCampo( 'cod_uf' )] = $rsUF->getCampo( 'nom_uf' );
    $rsUF->proximo();
}
$rsUF->setPrimeiroElemento();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
if (isset($stCtrl)) {
    $obHdnCtrl->setValue ( $stCtrl );
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
if (isset($stAcao)) {
    $obHdnAcao->setValue ( $stAcao );
}

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCLogradouros.php" );

$obCmbTipoLogradouro = new Select;
$obCmbTipoLogradouro->setRotulo       ( "Tipo de Logradouro"     );
$obCmbTipoLogradouro->setName         ( "inCodTipoLogradouro"     );
$obCmbTipoLogradouro->setId           ( "inCodTipoLogradouro"     );
$obCmbTipoLogradouro->addOption       ( "", "Selecione"           );
$obCmbTipoLogradouro->setCampoId      ( "cod_tipo"                );
$obCmbTipoLogradouro->setCampoDesc    ( "nom_tipo"                );
$obCmbTipoLogradouro->preencheCombo   ( $rsTipoLogradouro         );
$obCmbTipoLogradouro->setNull         ( true                      );
$obCmbTipoLogradouro->setStyle        ( "width: 220px"            );

$obTxtNom = new TextBox;
$obTxtNom->setName     ( "stNomLogradouro"        );
$obTxtNom->setRotulo   ( "Nome do Logradouro" );
$obTxtNom->setTitle    ( "" );
$obTxtNom->setSize     ( "80" ) ;
$obTxtNom->setMaxLength( "80" ) ;

$obCodInicio = new TextBox;
$obCodInicio->setName   ( "inCodInicio" );
$obCodInicio->setInteiro( true );
$obCodInicio->setRotulo ( "Código do Logradouro" );
$obCodInicio->setTitle  ( "Informe um período" ) ;

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName     ( "inCodTermino" );
$obCodTermino->setInteiro  ( true );
$obCodTermino->setRotulo   ( "Código do Logradouro" );
$obCodTermino->setTitle    ( "Informe um período" );

$obTxtNomBairro = new TextBox;
$obTxtNomBairro->setName     ( "stNomBairro"        );
$obTxtNomBairro->setRotulo   ( "Nome do Bairro"     );
$obTxtNomBairro->setTitle    ( "" );
$obTxtNomBairro->setSize     ( "80" ) ;
$obTxtNomBairro->setMaxLength( "80" ) ;

$obCodInicioBairro = new TextBox;
$obCodInicioBairro->setInteiro ( true );
$obCodInicioBairro->setName  ( "inCodInicioBairro" );
$obCodInicioBairro->setRotulo( "Código do Bairro" );
$obCodInicioBairro->setTitle ( "Informe um período" ) ;

$obLblPeriodoBairro = new Label;
$obLblPeriodoBairro->setValue( " até " );

$obCodTerminoBairro = new TextBox;
$obCodTerminoBairro->setName     ( "inCodTerminoBairro" );
$obCodTerminoBairro->setInteiro  ( true );
$obCodTerminoBairro->setRotulo   ( "Código do Bairro" );
$obCodTerminoBairro->setTitle    ( "Informe um período" );

$obCEPInicio = new Cep;
$obCEPInicio->setName  ( "inCEPInicio" );
$obCEPInicio->setRotulo( "CEP" );
$obCEPInicio->setTitle ( "Informe um período" ) ;

$obLblCEP = new Label;
$obLblCEP->setValue( " até " );

$obCEPTermino = new Cep;
$obCEPTermino->setName     ( "inCEPTermino" );
$obCEPTermino->setRotulo   ( "CEP" );
$obCEPTermino->setTitle    ( "Informe um período" );

$obTxtCodUF = new TextBox;
$obTxtCodUF->setRotulo             ( "Estado"                 );
$obTxtCodUF->setName               ( "inCodigoUF"             );
$obTxtCodUF->setValue              ( $request->get("inCodigoUF"));
$obTxtCodUF->setSize               ( 8                        );
$obTxtCodUF->setMaxLength          ( 8                        );
$obTxtCodUF->setNull               ( true                     );
$obTxtCodUF->setInteiro            ( true                     );
$obTxtCodUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio');" );

$obCmbUF = new Select;
$obCmbUF->setName               ( "inCodUF"                 );
$obCmbUF->addOption             ( "", "Selecione"           );
$obCmbUF->setCampoId            ( "cod_uf"                  );
$obCmbUF->setCampoDesc          ( "nom_uf"                  );
$obCmbUF->preencheCombo         ( $rsUF                     );
$obCmbUF->setValue              ( $request->get("inCodigoUF"));
$obCmbUF->setNull               ( true                      );
$obCmbUF->setStyle              ( "width: 220px"            );
$obCmbUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio');"  );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo             ( "Município"             );
$obTxtCodMunicipio->setName               ( "inCodigoMunicipio"            );
$obTxtCodMunicipio->setValue              ( $request->get("inCodigoMunicipio"));
$obTxtCodMunicipio->setSize               ( 8                              );
$obTxtCodMunicipio->setMaxLength          ( 8                              );
$obTxtCodMunicipio->setNull               ( true                           );
$obTxtCodMunicipio->setInteiro            ( true                           );

$obCmbMunicipio = new Select;
$obCmbMunicipio->setName               ( "inCodMunicipio"               );
$obCmbMunicipio->addOption             ( "", "Selecione"                );
$obCmbMunicipio->setCampoId            ( "cod_municipio"                );
$obCmbMunicipio->setCampoDesc          ( "nom_municipio"                );
$obCmbMunicipio->setValue              ( $request->get("inCodigoMunicipio"));
$obCmbMunicipio->setNull               ( true                           );
$obCmbMunicipio->setStyle              ( "width: 220px"                 );

$sessao->nomFiltro['ordenacao']['codlogradouro'] = "Código do Logradouro";
$sessao->nomFiltro['ordenacao']['nomlogradouro'] = "Nome do Logradouro";

$obCmbOrder = new Select;
$obCmbOrder->setName               ( "stOrder"                 );
$obCmbOrder->setRotulo             ( "Ordenação"               );
$obCmbOrder->setTitle              ( "Escolha a ordenação do relatório" );
$obCmbOrder->addOption             ( "", "Selecione"           );
$obCmbOrder->addOption             ( "codlogradouro" , "Código do Logradouro"  );
$obCmbOrder->addOption             ( "nomlogradouro" , "Nome do Logradouro"    );
$obCmbOrder->setCampoDesc          ( "stOrder"        );
$obCmbOrder->setNull               ( false            );
$obCmbOrder->setStyle              ( "width: 200px"   );

// Mostrar Historico do logradouro
$obRadMostrarHistoricoSim = new Radio();
$obRadMostrarHistoricoSim->setId     ('boHistorico');
$obRadMostrarHistoricoSim->setName   ('boHistorico');
$obRadMostrarHistoricoSim->setValue  ('S');
$obRadMostrarHistoricoSim->setRotulo ('Mostrar Histórico do Logradouro');
$obRadMostrarHistoricoSim->setLabel  ('Sim');

$obRadMostrarHistoricoNao = new Radio();
$obRadMostrarHistoricoNao->setId      ('boHistorico');
$obRadMostrarHistoricoNao->setName    ('boHistorico');
$obRadMostrarHistoricoNao->setValue   ('N');
$obRadMostrarHistoricoNao->setRotulo  ('Mostrar Histórico do Logradouro');
$obRadMostrarHistoricoNao->setLabel   ('Não');
$obRadMostrarHistoricoNao->setChecked (true);

$arMostrarHistorico = array($obRadMostrarHistoricoSim, $obRadMostrarHistoricoNao);

// Mostrar Norma do Logradouro
$obRadMostrarNormaSim = new Radio();
$obRadMostrarNormaSim->setId     ('boNorma');
$obRadMostrarNormaSim->setName   ('boNorma');
$obRadMostrarNormaSim->setValue  ('S');
$obRadMostrarNormaSim->setRotulo ('Demonstrar Norma do Logradouro');
$obRadMostrarNormaSim->setLabel  ('Sim');

$obRadMostrarNormaNao = new Radio();
$obRadMostrarNormaNao->setId      ('boNorma');
$obRadMostrarNormaNao->setName    ('boNorma');
$obRadMostrarNormaNao->setValue   ('N');
$obRadMostrarNormaNao->setRotulo  ('Demonstrar Norma do Logradouro');
$obRadMostrarNormaNao->setLabel   ('Não');
$obRadMostrarNormaNao->setChecked (true);

$arMostrarNorma = array($obRadMostrarNormaSim, $obRadMostrarNormaNao);

$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm );
$obFormulario->setAjuda              ( "UC-05.01.20" );
$obFormulario->addHidden             ( $obHdnCaminho );
$obFormulario->addHidden             ( $obHdnAcao    );
$obFormulario->addHidden             ( $obHdnCtrl    );
$obFormulario->addTitulo             ( "Dados para filtro"        );
$obFormulario->addComponente         ( $obCmbTipoLogradouro );
$obFormulario->addComponente         ( $obTxtNom );
$obFormulario->agrupaComponentes     ( array( $obCodInicio, $obLblPeriodo ,$obCodTermino) );
$obFormulario->addComponente         ( $obTxtNomBairro );
$obFormulario->agrupaComponentes     ( array( $obCodInicioBairro, $obLblPeriodoBairro ,$obCodTerminoBairro ) );
$obFormulario->agrupaComponentes     ( array( $obCEPInicio, $obLblCEP ,$obCEPTermino) );
$obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF               );
$obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
$obFormulario->addComponente         ( $obCmbOrder );
$obFormulario->agrupaComponentes     ( $arMostrarHistorico );
$obFormulario->agrupaComponentes     ( $arMostrarNorma );

$obFormulario->OK();
$obFormulario->setFormFocus          ( $obCmbTipoLogradouro->getid() );
$obFormulario->show();
?>
