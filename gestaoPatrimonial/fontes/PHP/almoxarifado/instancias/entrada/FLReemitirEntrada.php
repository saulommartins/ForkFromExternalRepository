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
    * Página de filtro
    * Data de Criação: 23/03/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    * Caso de uso: uc-03.03.31

    $Id:$

    */

# Includes de Framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

# Classes de Mapeamento
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNatureza.class.php";

# Componentes do Almoxarifado
include_once CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";

# Define o nome dos arquivos PHP
$stPrograma = "ReemitirEntrada";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$stAcao = $request->get('stAcao');

Sessao::write('arFiltro' , array());

# Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

# Componente Pop-Up para buscar o Id do Item.
$obItem = new IPopUpItem        ( $obForm     );
$obItem->setAtivo               ( true        );
$obItem->setUnidadeNaoInformado ( true        );
$obItem->setTipoNaoInformado    ( true        );
$obItem->setServico             ( true        );
$obItem->setNull                ( true        );
$obItem->setObrigatorioBarra    ( true        );
$obItem->obCampoCod->setId      ( 'inCodItem' );

# Componente Pop-Up para Fornecedor.
$obIPopUpFornecedor = new IPopUpCGMVinculado( $obForm                 );
$obIPopUpFornecedor->setTabelaVinculo       ( 'compras.fornecedor'    );
$obIPopUpFornecedor->setCampoVinculo        ( 'cgm_fornecedor'        );
$obIPopUpFornecedor->setNomeVinculo         ( 'Fornecedor'            );
$obIPopUpFornecedor->setRotulo              ( 'Fornecedor'            );
$obIPopUpFornecedor->setTitle               ( 'Informe o fornecedor.' );
$obIPopUpFornecedor->setName                ( 'stNomCGM'              );
$obIPopUpFornecedor->setId                  ( 'stNomCGM'              );
$obIPopUpFornecedor->obCampoCod->setName    ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setId      ( 'inCGM'                 );
$obIPopUpFornecedor->obCampoCod->setNull    ( true                    );
$obIPopUpFornecedor->setNull                ( true                    );

# Recupera os tipos de entradas ja utilizados.
$obTAlmoxarifadoNatureza = new TAlmoxarifadoNatureza;
$stFiltro  = " WHERE  tipo_natureza = 'E'                                                                             \n";
$stFiltro .= "   AND  natureza.cod_natureza not in (5,6)                                                              \n";
$stFiltro .= "   AND  EXISTS (SELECT 1                                                                                \n";
$stFiltro .= "               FROM almoxarifado.natureza_lancamento                                                    \n";
$stFiltro .= "                  , almoxarifado.lancamento_material                                                    \n";
$stFiltro .= "              WHERE natureza_lancamento.cod_natureza         = natureza.cod_natureza                    \n";
$stFiltro .= "                AND natureza_lancamento.tipo_natureza        = natureza.tipo_natureza                   \n";
$stFiltro .= "                AND lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento \n";
$stFiltro .= "                AND lancamento_material.num_lancamento       = natureza_lancamento.num_lancamento       \n";
$stFiltro .= "                AND lancamento_material.cod_natureza         = natureza_lancamento.cod_natureza         \n";
$stFiltro .= "                AND lancamento_material.tipo_natureza        = natureza_lancamento.tipo_natureza        \n";
$stFiltro .= "            )                                                                                           \n";
$stOrdem   = " ORDER BY natureza.descricao                                                                            \n";
$obTAlmoxarifadoNatureza->recuperaTodos($rsNaturezaEntrada, $stFiltro, $stOrdem);

# Naturezas.
$obISelectMultiploNatureza = new SelectMultiplo;
$obISelectMultiploNatureza->setName   ('stNatureza'           );
$obISelectMultiploNatureza->setRotulo ( "Natureza de Entrada" );
$obISelectMultiploNatureza->setNull   ( false                 );
$obISelectMultiploNatureza->setTitle  ( "Selecione a(s) Natureza(s) de Entrada." );

# Objeto que apresenta as Naturezas disponiveis.
$obISelectMultiploNatureza->SetNomeLista1 ('inCodNatureza'     );
$obISelectMultiploNatureza->setCampoId1   ('cod_natureza'      );
$obISelectMultiploNatureza->setCampoDesc1 ('descricao'         );
$obISelectMultiploNatureza->SetRecord1    ( $rsNaturezaEntrada );

# Objeto que apresenta as Naturezas Selecionadas.
$obISelectMultiploNatureza->SetNomeLista2 ( 'inCodNaturezaSelecionados');
$obISelectMultiploNatureza->setCampoId2   ( 'cod_natureza'  );
$obISelectMultiploNatureza->setCampoDesc2 ( 'descricao'     );
$obISelectMultiploNatureza->SetRecord2    ( new RecordSet   );

# Componente Periodicidade.
$obPeriodicidade = new Periodicidade;
$obPeriodicidade->setRotulo    ( "Periodicidade"        );
$obPeriodicidade->setTitle     ( "Informe a periodicidade do lançamento." );
$obPeriodicidade->setName      ( "dtNatureza"           );
$obPeriodicidade->setNull      ( false                  );
$obPeriodicidade->setExercicio ( Sessao::getExercicio() );

# Objeto para filtrar pelo numero de saída.
$obTxtNumEntrada = new TextBox;
$obTxtNumEntrada->setName      ( "inNumEntrada"                 );
$obTxtNumEntrada->setValue     ( ""                             );
$obTxtNumEntrada->setRotulo    ( "Número de Entrada"            );
$obTxtNumEntrada->setTitle     ( "Informe o número da entrada." );
$obTxtNumEntrada->setNull      ( true                           );
$obTxtNumEntrada->setMaxLength ( 4                              );
$obTxtNumEntrada->setSize      ( 5                              );

# Monta Formulário
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-03.03.31");
$obFormulario->addHidden     ( $obHdnCtrl                 );
$obFormulario->addHidden     ( $obHdnAcao                 );
$obFormulario->addTitulo     ( "Dados para filtro"        );
$obFormulario->addComponente ( $obItem                    );
$obFormulario->addComponente ( $obIPopUpFornecedor        );
$obFormulario->addComponente ( $obISelectMultiploNatureza );
$obFormulario->addComponente ( $obPeriodicidade           );
$obFormulario->addComponente ( $obTxtNumEntrada           );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
