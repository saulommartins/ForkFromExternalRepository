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
    * Página de Filtro para exportação de ITBI/IPTU
    * Data de Criação   : 05/06/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Casos de uso: uc-06.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';

$stPrograma = "ExportacaoGT";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

Sessao::write('arArquivosDownload', array());

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$stOrdem = "ORDER BY cod_entidade";
$obROrcamentoEntidade->listarEntidades( $rsEntidades, $stOrdem );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'telaPrincipal' );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( 'stCtrl' );
$obHdnCtrl->setValue( '' );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

// Define o objeto para o periodo da exportacao
$obCmbSemestre = new Select;
$obCmbSemestre->setRotulo( "Semestre" );
$obCmbSemestre->setName( "cmbSemestre" );
$obCmbSemestre->addOption( "", "Selecione" );
$obCmbSemestre->addOption( "1", "1º Semestre" );
$obCmbSemestre->addOption( "2", "2º Semestre" );
$obCmbSemestre->setNull( false );
$obCmbSemestre->setStyle( "width: 220px" );

$arArquivos = array(
    array(
        'arquivo' => 'iptu',
        'nome' => 'IPTU'
    ),
    array(
        'arquivo' => 'itbi_urbano',
        'nome' => 'ITBI URBANO'
    ),
    array(
        'arquivo' => 'itbi_rural',
        'nome' => 'ITBI RURAL'
    ),
    array(
        'arquivo' => 'pl_valores_urbanos_itbi_iptu',
        'nome' => 'PLANTA DE VALORES - IMÓVEIS URBANOS DE ITBI/IPTU'
    ),
    array(
        'arquivo' => 'pl_valores_rurais_itbi',
        'nome' => 'PLANTA DE VALORES - IMÓVEIS RURAIS DE ITBI'
    ),
    array(
        'arquivo' => 'cadastro_logradouro',
        'nome' => 'CADASTRO DE LOGRADOUROS'
    )
);

$rsArquivos = new RecordSet;
$rsArquivos->preenche($arArquivos);

// Define SELECT multiplo para os arquivos
$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName( 'arArquivos' );
$obCmbArquivos->setRotulo( 'Arquivos' );
$obCmbArquivos->setTitle( '' );
$obCmbArquivos->setNull( false );

// lista as entidades disponiveis
$obCmbArquivos->SetNomeLista1( 'arArquivoDisponivel' );
$obCmbArquivos->setCampoId1( 'arquivo' );
$obCmbArquivos->setCampoDesc1( 'nome' );
$obCmbArquivos->SetRecord1( $rsArquivos );
// lista as entidades selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivos' );
$obCmbArquivos->setCampoId2( 'arquivo' );
$obCmbArquivos->setCampoDesc2( 'nome' );
$obCmbArquivos->SetRecord2( new RecordSet );

// Define o objeto do Formulario
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( 'Dados para Filtro' );
$obFormulario->addComponente( $obCmbSemestre );
$obFormulario->addComponente( $obCmbArquivos );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
