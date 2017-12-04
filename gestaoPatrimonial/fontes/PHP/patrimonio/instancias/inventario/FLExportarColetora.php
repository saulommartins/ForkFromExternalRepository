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
    * Página de Filtro para exportação de arquivo para coletora de dados

    * @date: 04/08/2010

    * @author: Analista: Gelson
    * @author: Desenvolvedor: Tonismar

    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO.'ROrganogramaLocal.class.php';

/** Pega a ação **/
$stAcao = $_POST['stAcao'] ? $_POST['stAcao'] : $_GET['stAcao'];

/** Criação do array com os arquiveis possíveis **/
$arquivos = array( array('Arquivo' => 'Cadastro.txt', 'Nome' => 'Cadastro.txt'),
                   array('Arquivo' => 'Inventario.txt', 'Nome' => 'Inventario.txt')
                );

/** Recuperando os locais no organograma **/
$local = new ROrganogramaLocal();
$local->listarLocal($listaLocais);

$locaisSelecionados = new RecordSet;

/** Select para selecionar locais **/
$selectLocais = new SelectMultiplo();
$selectLocais->setName('locaisSelecionados');
$selectLocais->setRotulo('Locais');
$selectLocais->setNull(false);
$selectLocais->setTitle('Locais Disponíveis');

$selectLocais->SetNomeLista1('locaisDisponiveis');
$selectLocais->setCampoId1('cod_local');
$selectLocais->setCampoDesc1('descricao');
$selectLocais->setRecord1($locaisSelecionados);

$selectLocais->SetNomeLista2('locaisSelecionados');
$selectLocais->setCampoId2('cod_local');
$selectLocais->setCampoDesc2('descricao');
$selectLocais->setRecord2($listaLocais);

$listaSelecionados = new RecordSet;
$listaArquivos = new RecordSet;
$listaArquivos->preenche( $arquivos );

/** Select para selecionar arquivos **/
$selectArquivos = new SelectMultiplo();
$selectArquivos->setName('arquivosSelecionados');
$selectArquivos->setRotulo('Arquivos');
$selectArquivos->setNull(false);
$selectArquivos->setTitle('Arquivos Disponíveis');

$selectArquivos->SetNomeLista1('listaDisponiveis');
$selectArquivos->setCampoId1('Arquivo');
$selectArquivos->setCampoDesc1('Nome');
$selectArquivos->setRecord1($listaArquivos);

$selectArquivos->SetNomeLista2('listaSelecionados');
$selectArquivos->setCampoId2('Arquivo');
$selectArquivos->setCampoDesc2('Nome');
$selectArquivos->setRecord2($listaSelecionados);

$hiddenOculto = new Hidden();
$hiddenOculto->setName('hdnPaginaExportacao');
$hiddenOculto->setValue('../../../patrimonio/instancias/inventario/OCExportarColetora.php');

$hiddenAction = new Hidden();
$hiddenAction->setName('stAcao');
$hiddenAction->setValue($stAcao);

$form = new Form();
$form->setAction('../../../exportacao/instancias/processamento/PRExportador.php');
$form->setTarget('telaPrincipal');

$formulario = new Formulario();
$formulario->addForm($form);
$formulario->addHidden($hiddenAction);
$formulario->addHidden($hiddenOculto);
$formulario->addTitulo('Dados para exportar arquivos');
$formulario->addComponente($selectArquivos);
$formulario->addComponente($selectLocais);
$formulario->Ok();
$formulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
