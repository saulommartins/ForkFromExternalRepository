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
  * Página de formulário da Configuração de Metas de Arrecadacao da Receita
  * Data de Criação: 22/01/2015

  * @author Analista: Ane Caroline
  * @author Desenvolvedor: Lisiane Morais

  * @ignore
  *
  * $Id:$
  *
  * $Revision:$
  * $Author:$
  * $Date:$

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOMetasArrecadacaoReceita.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoMetasArrecadacaoReceita";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$rsTTCMGOMetasArrecadacaoReceita = new RecordSet();
$obTTCMGOMetasArrecadacaoReceita = new TTCMGOMetasArrecadacaoReceita();
$obTTCMGOMetasArrecadacaoReceita->setDado('exercicio',$_REQUEST['inExercicio']);
$obTTCMGOMetasArrecadacaoReceita->recuperaMetasArrecadacaoReceita($rsTTCMGOMetasArrecadacaoReceita);

if ($rsTTCMGOMetasArrecadacaoReceita->getNumLinhas() > 0) {
    $vlMetaArrecadacao1Bi = number_format($rsTTCMGOMetasArrecadacaoReceita->getCampo('meta_arrecadacao_1_bi'),2,",",".");
    $vlMetaArrecadacao2Bi = number_format($rsTTCMGOMetasArrecadacaoReceita->getCampo('meta_arrecadacao_2_bi'),2,",",".");
    $vlMetaArrecadacao3Bi = number_format($rsTTCMGOMetasArrecadacaoReceita->getCampo('meta_arrecadacao_3_bi'),2,",",".");
    $vlMetaArrecadacao4Bi = number_format($rsTTCMGOMetasArrecadacaoReceita->getCampo('meta_arrecadacao_4_bi'),2,",",".");
    $vlMetaArrecadacao5Bi = number_format($rsTTCMGOMetasArrecadacaoReceita->getCampo('meta_arrecadacao_5_bi'),2,",",".");
    $vlMetaArrecadacao6Bi = number_format($rsTTCMGOMetasArrecadacaoReceita->getCampo('meta_arrecadacao_6_bi'),2,",",".");
   
} else {
    $vlMetaArrecadacao1Bi = '0,00';
    $vlMetaArrecadacao2Bi = '0,00';
    $vlMetaArrecadacao3Bi = '0,00';
    $vlMetaArrecadacao4Bi = '0,00';
    $vlMetaArrecadacao5Bi = '0,00';
    $vlMetaArrecadacao6Bi = '0,00';
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnStExercicio = new Hidden();
$obHdnStExercicio->setName('stExercicio');
$obHdnStExercicio->setValue($_REQUEST['inExercicio']);

//****************************************//
//Monta valores dos Bimestres
//****************************************//
$obValorMetaArrecadacao1Bi = new Numerico();
$obValorMetaArrecadacao1Bi->setId    ('valorMetaArrecadacao1Bi');
$obValorMetaArrecadacao1Bi->setName  ('valorMetaArrecadacao1Bi');
$obValorMetaArrecadacao1Bi->setRotulo('Meta de arrecadação do 1º Bimestre');
$obValorMetaArrecadacao1Bi->setTitle ('Informar o valor da meta de arrecadação do 1º bimestre.');
$obValorMetaArrecadacao1Bi->setDecimais(2);
$obValorMetaArrecadacao1Bi->setMaxLength(15);
$obValorMetaArrecadacao1Bi->setSize(17);
$obValorMetaArrecadacao1Bi->setValue($vlMetaArrecadacao1Bi);

$obValorMetaArrecadacao2Bi = new Numerico();
$obValorMetaArrecadacao2Bi->setId    ('valorMetaArrecadacao2Bi');
$obValorMetaArrecadacao2Bi->setName  ('valorMetaArrecadacao2Bi');
$obValorMetaArrecadacao2Bi->setRotulo('Meta de arrecadação do 2º Bimestre');
$obValorMetaArrecadacao2Bi->setTitle ('Informar o valor da meta de arrecadação do 2º bimestre.');
$obValorMetaArrecadacao2Bi->setDecimais(2);
$obValorMetaArrecadacao2Bi->setMaxLength(15);
$obValorMetaArrecadacao2Bi->setSize(17);
$obValorMetaArrecadacao2Bi->setValue($vlMetaArrecadacao2Bi);

$obValorMetaArrecadacao3Bi = new Numerico();
$obValorMetaArrecadacao3Bi->setId    ('valorMetaArrecadacao3Bi');
$obValorMetaArrecadacao3Bi->setName  ('valorMetaArrecadacao3Bi');
$obValorMetaArrecadacao3Bi->setRotulo('Meta de arrecadação do 3º Bimestre');
$obValorMetaArrecadacao3Bi->setTitle ('Informar o valor da meta de arrecadação do 3º bimestre.');
$obValorMetaArrecadacao3Bi->setDecimais(2);
$obValorMetaArrecadacao3Bi->setMaxLength(15);
$obValorMetaArrecadacao3Bi->setSize(17);
$obValorMetaArrecadacao3Bi->setValue($vlMetaArrecadacao3Bi);

$obValorMetaArrecadacao4Bi = new Numerico();
$obValorMetaArrecadacao4Bi->setId    ('valorMetaArrecadacao4Bi');
$obValorMetaArrecadacao4Bi->setName  ('valorMetaArrecadacao4Bi');
$obValorMetaArrecadacao4Bi->setRotulo('Meta de arrecadação do 4º Bimestre');
$obValorMetaArrecadacao4Bi->setTitle ('Informar o valor da meta de arrecadação do 4º bimestre.');
$obValorMetaArrecadacao4Bi->setDecimais(2);
$obValorMetaArrecadacao4Bi->setMaxLength(15);
$obValorMetaArrecadacao4Bi->setSize(17);
$obValorMetaArrecadacao4Bi->setValue($vlMetaArrecadacao4Bi);

$obValorMetaArrecadacao5Bi = new Numerico();
$obValorMetaArrecadacao5Bi->setId    ('valorMetaArrecadacao5Bi');
$obValorMetaArrecadacao5Bi->setName  ('valorMetaArrecadacao5Bi');
$obValorMetaArrecadacao5Bi->setRotulo('Meta de arrecadação do 5º Bimestre');
$obValorMetaArrecadacao5Bi->setTitle ('Informar o valor da meta de arrecadação do 5º bimestre.');
$obValorMetaArrecadacao5Bi->setDecimais(2);
$obValorMetaArrecadacao5Bi->setMaxLength(15);
$obValorMetaArrecadacao5Bi->setSize(17);
$obValorMetaArrecadacao5Bi->setValue($vlMetaArrecadacao5Bi);

$obValorMetaArrecadacao6Bi = new Numerico();
$obValorMetaArrecadacao6Bi->setId    ('valorMetaArrecadacao6Bi');
$obValorMetaArrecadacao6Bi->setName  ('valorMetaArrecadacao6Bi');
$obValorMetaArrecadacao6Bi->setRotulo('Meta de arrecadação do 6º Bimestre');
$obValorMetaArrecadacao6Bi->setTitle ('Informar o valor da meta de arrecadação do 6º bimestre.');
$obValorMetaArrecadacao6Bi->setDecimais(2);
$obValorMetaArrecadacao6Bi->setMaxLength(15);
$obValorMetaArrecadacao6Bi->setSize(17);
$obValorMetaArrecadacao6Bi->setValue($vlMetaArrecadacao6Bi);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm         ( $obForm );
$obFormulario->addHidden       ( $obHdnCtrl );
$obFormulario->addHidden       ( $obHdnAcao );
$obFormulario->addHidden       ( $obHdnStExercicio );
$obFormulario->setLarguraRotulo( 30 );
$obFormulario->setLarguraCampo ( 70 );
$obFormulario->addTitulo       ( "Detalhamento das Metas das Arrecadações de Receitas" );
$obFormulario->addComponente   ( $obValorMetaArrecadacao1Bi );
$obFormulario->addComponente   ( $obValorMetaArrecadacao2Bi );
$obFormulario->addComponente   ( $obValorMetaArrecadacao3Bi );
$obFormulario->addComponente   ( $obValorMetaArrecadacao4Bi );
$obFormulario->addComponente   ( $obValorMetaArrecadacao5Bi );
$obFormulario->addComponente   ( $obValorMetaArrecadacao6Bi );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
