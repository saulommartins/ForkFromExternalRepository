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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 24/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 62954 $
    $Name$
    $Autor: $
    $Date: 2008-08-18 09:58:01 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTBATipoBem.class.php" );

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoBem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTTBATipoBem = new TTBATipoBem();
$obTTBATipoBem->recuperaNaturezaGrupo($rsRecordSet,' ORDER BY grupo.cod_natureza, grupo.cod_grupo');

$obLista = new Lista;
$obLista->setTitulo ( "Relacionamento com Documentos Exigidos - Tipos de Certidão TCMBA");
$obLista->setRecordSet ($rsRecordSet );
$obLista->setMostraPaginacao( false );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza/Grupo - Sistema" );
$obLista->ultimoCabecalho->setWidth( 67 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Bem - TCM" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_natureza]-[nom_natureza] / [cod_grupo]-[nom_grupo]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obCmbCombo = new Select();
$obCmbCombo->setName          ("inTipo_[cod_natureza]_[cod_grupo]_" );
$obCmbCombo->setTitle         ("Selecione"                  );
$obCmbCombo->addOption        ("","Selecione"               );
$obCmbCombo->addOption        ('1','1 - Móveis, Utensílios e Mobiliários');
$obCmbCombo->addOption        ('2','2 - Máquinas, Motores e Geradores');
$obCmbCombo->addOption        ('3','3 - Equipamentos, Instrumentos, Instrumentos Musicais e Ferramentas');
$obCmbCombo->addOption        ('4','4 - Semoventes');
$obCmbCombo->addOption        ('5','5 - Biblioteca');
$obCmbCombo->addOption        ('6','6 - Imóveis');
$obCmbCombo->addOption        ('7','7 - Diversos bens móveis e Objeto de Arte');
$obCmbCombo->addOption        ('8','8 - Natureza Industrial');
$obCmbCombo->addOption        ('9','9 - Veículos');
$obCmbCombo->setNull          ( false                       );
$obCmbCombo->setValue         ("[cod_tipo_tcm]");

$obLista->addDadoComponente( $obCmbCombo );
$obLista->ultimoDado->setCampo( "[cod_natureza]_[cod_grupo]" );
$obLista->commitDadoComponente();
$obLista->montaInnerHTML();

$stLista = $obLista->getHTML();

//Define Span para DataGrid
$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );
$obSpnLista->setValue ( $stLista );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addSpan( $obSpnLista );

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

SistemaLegado::LiberaFrames();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>