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
    * Página que Filtra os Processos Iniciados para Prorrogar o Recebimento de Documentos
    * Data de Criacao: 12/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../cadastroEconomico/classes/componentes/IPopUpEmpresa.class.php';

$stAcao = 'cadastrar';

//Define o nome dos arquivos PHP
$stPrograma = "ManterLevantamento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

//Campos Hidden
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST['stCtrl'] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "numcgm" );
$obHdnCtrl->setValue(substr($_SESSION['numCgm'], 5,-2) );

//Definição do Form
$obForm = new Form;
$obForm->setAction ( $pgList );
$obForm->setTarget ( "telaPrincipal" );

//Tipo Fiscalizacao
$obTipoFiscalizacao =  new Hidden;
$obTipoFiscalizacao->setName ( "inTipoFiscalizacao" );
$obTipoFiscalizacao->setValue("1");

//Tipo Fiscalização
$obLTipoFiscalizacao = new Label;
$obLTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obLTipoFiscalizacao->setName( "stTipoFiscalizacao" );
$obLTipoFiscalizacao->setId( "stTipoFiscalizacao" );
$obLTipoFiscalizacao->setValue( "01 - Fiscalização Tributária do ISSQN" );

//Processo Fiscal
$obProcessoFiscal = new TextBox;
$obProcessoFiscal->setName( "inCodProcesso" );
$obProcessoFiscal->setId( "inCodProcesso" );
$obProcessoFiscal->setSize( "10" );
$obProcessoFiscal->setRotulo( "Processo Fiscal" );
$obProcessoFiscal->setTitle( "Informe o Código do Processo Fiscal." );
$obProcessoFiscal->setNull( true );

$obInscricaoEconomica = new IPopUpEmpresa;
$obInscricaoEconomica->obInnerEmpresa->setNull(true);

$obRServico = new Radio;
$obRServico->setName("bt_faturamento");
$obRServico->setRotulo("Tipo Faturamento");
$obRServico->setTitle("Informe o tipo de faturamento a ser utilizado");
$obRServico->setValue("servico");
$obRServico->setLabel("por Serviço");
$obRServico->setChecked(true);
$obRServico->setNull(false);

$obRNota = new Radio;
$obRNota->setName("bt_faturamento");
$obRNota->setValue("nota");
$obRNota->setLabel("por Nota");
$obRNota->setNull(false);

$obRRetido = new Radio;
$obRRetido->setName("bt_faturamento");
$obRRetido->setValue("retido");
$obRRetido->setLabel("Retido na Fonte");
$obRRetido->setNull(false);

$obRadio = array($obRServico, $obRNota,$obRRetido);
//Novo Formulário
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Dados para Filtro");
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obTipoFiscalizacao);
$obFormulario->addComponente($obLTipoFiscalizacao);
$obFormulario->addComponente($obProcessoFiscal);
$obInscricaoEconomica->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes($obRadio);

$obFormulario->Ok(true);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
