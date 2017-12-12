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
    * Gerar o componente o SelectMultiploRegSubCarEsp
    * Data de Criação: 21/03/2006

    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes

    Casos de uso: uc-04.04.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php" );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php");
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php");

/**
    * Cria o componente SelectMultiplo para Regime/SubDivisão/Cargo/Especialidade
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploRegSubCarEsp
{
/**
   * @access Private
   * @var Object
*/
var $obRPessoalRegime;
/**
   * @access Private
   * @var Object
*/
var $obCmbRegime;
/**
   * @access Private
   * @var Object
*/
var $obCmbSubDivisao;
/**
   * @access Private
   * @var Object
*/
var $obCmbCargo;
/**
   * @access Private
   * @var Object
*/
var $obCmbFuncao;
/**
   * @access Private
   * @var Object
*/
var $obCmbEspecialidade;
/**
   * @access Private
   * @var Boolean
*/
var $boFuncao;
/**
   * @access Private
   * @var Boolean
*/
var $boDisabledFuncao;
/**
   * @access Private
   * @var Boolean
*/
var $boDisabledCargo;
/**
   * @access Private
   * @var Boolean
*/
var $boDisabledEspecialidade;

/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalRegime($valor) { $this->obRPessoalRegime    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmbRegime($valor) { $this->obCmbRegime         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmbSubDivisao($valor) { $this->obCmbSubDivisao     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmbCargo($valor) { $this->obCmbCargo          = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmbFuncao($valor) { $this->obCmbFuncao         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmbEspecialidade($valor) { $this->obCmbEspecialidade  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setFuncao($valor) { $this->boFuncao            = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setDisabledFuncao($valor) { $this->boDisabledFuncao    = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setDisabledCargo($valor) { $this->boDisabledCargo     = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setDisabledEspecialidade($valor) { $this->boDisabledEspecialidade= $valor; }

/**
    * @access Public
    * @return Object
*/
function getRPessoalRegime() { return $this->obRPessoalRegime;    }
/**
    * @access Public
    * @return Object
*/
function getCmbRegime() { return $this->obCmbRegime;         }
/**
    * @access Public
    * @return Object
*/
function getCmbSubDivisao() { return $this->obCmbSubDivisao;     }
/**
    * @access Public
    * @return Object
*/
function getCmbCargo() { return $this->obCmbCargo;          }
/**
    * @access Public
    * @return Object
*/
function getCmbFuncao() { return $this->obCmbFuncao;        }
/**
    * @access Public
    * @return Object
*/
function getCmbEspecialidade() { return $this->obCmbEspecialidade;  }
/**
    * @access Public
    * @return Boolean
*/
function getFuncao() { return $this->boFuncao;            }
/**
    * @access Public
    * @return Boolean
*/
function getDisabledFuncao() { return $this->boDisabledFuncao;    }
/**
    * @access Public
    * @return Boolean
*/
function getDisabledCargo() { return $this->boDisabledCargo;    }
/**
    * @access Public
    * @return Boolean
*/
function getDisabledEspecialidade() { return $this->boDisabledEspecialidade;}

/**
    * Método Construtor
    * @access Public
*/
function ISelectMultiploRegSubCarEsp($boFuncao = false)
{
    $this->setFuncao( $boFuncao );
    $this->obRPessoalRegime = new RPessoalRegime;
    $this->obRPessoalRegime->listarRegime( $rsRegime );

    $obPessoalRegime = new TPessoalRegime();
    $obPessoalRegime->recuperaTodos($rsCodigos);
    $stCodRegime = "";
    while (!$rsCodigos->eof()) {
        $stCodRegime .= $rsCodigos->getCampo('cod_regime') . ',';
        $rsCodigos->proximo();
    }

    $stCodRegime = substr($stCodRegime,0, -1);

    $obRPessoalSubDivisao = new RPessoalSubDivisao( $this->obRPessoalRegime );
    $obRPessoalSubDivisao->listarSubDivisaoDeCodigosRegime($rsSubDivisao, $stCodRegime);

    $arCodSubDivisaoCargo = "";
    $count = 0;

    while (!$rsSubDivisao->eof()) {
        $arCodSubDivisaoCargo[$count] = $rsSubDivisao->getCampo('cod_sub_divisao');
        $rsSubDivisao->proximo();
        $count++;
    }
    $rsSubDivisao->setPrimeiroElemento();
    $inCodSubDivisaoCargo = implode(",",$arCodSubDivisaoCargo);

    $obRPessoalSubDivisao->setCodSubDivisao($inCodSubDivisaoCargo);
    $obRPessoalSubDivisao->listarCargoEspecialidade($rsCargo);

    $count = 0;
    while (!$rsCargo->eof()) {
        if ($rsCargo->getCampo('cod_especialidade') != "") {
            $arDescricaoEspecialidade[$count]['descr_espec'] = $rsCargo->getCampo('descr_espec');
            $arDescricaoEspecialidade[$count]['cod_especialidade'] = $rsCargo->getCampo('cod_especialidade');
            $count++;
        }
        $rsCargo->proximo();
    }

    $rsCargo->setPrimeiroElemento();

    $rsEspecialidades = new RecordSet;
    $rsEspecialidades->preenche($arDescricaoEspecialidade);
    $rsEspecialidades->setPrimeiroElemento();

    $this->obCmbRegime = new SelectMultiplo();
    $this->obCmbRegime->setName         ( 'inCodRegime'                                                     );
    $this->obCmbRegime->setRotulo       ( "Regime"                                                          );
    $this->obCmbRegime->setTitle        ( "Selecione o(s) regime(s)."                                       );
    $this->obCmbRegime->SetNomeLista1   ( $this->getNameComponente('inCodRegimeDisponiveis')                );
    $this->obCmbRegime->setCampoId1     ( '[cod_regime]'                                                    );
    $this->obCmbRegime->setCampoDesc1   ( '[descricao]'                                                     );
    $this->obCmbRegime->setStyle1       ( "width: 300px"                                                    );
    $this->obCmbRegime->SetRecord1      ( new recordset                                                     );
    $this->obCmbRegime->SetNomeLista2   ( $this->getNameComponente('inCodRegimeSelecionados')               );
    $this->obCmbRegime->setCampoId2     ( '[cod_regime]'                                                    );
    $this->obCmbRegime->setCampoDesc2   ( '[descricao]'                                                     );
    $this->obCmbRegime->setStyle2       ( "width: 300px"                                                    );
    $this->obCmbRegime->SetRecord2      ( $rsRegime                                                     );
    $stOnClick = "if (document.frm.".$this->getNameComponente('inCodEspecialidadeSelecionados').") { selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodEspecialidadeSelecionados')."',true);}
                  if (document.frm.inCodFuncaoSelecionados) { selecionarSelectMultiploRegSubCarEsp('inCodFuncaoSelecionados',true); }
                  if (document.frm.inCodCargoSelecionados) { selecionarSelectMultiploRegSubCarEsp('inCodCargoSelecionados',true);  }
                  selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodSubDivisaoSelecionados')."',true);
                  selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodRegimeSelecionados')."',true);
                  buscaValorBscInner('".CAM_GRH_PES_PROCESSAMENTO."OCFiltroMultiploRegSubCarEsp.php?".Sessao::getId()."','frm','".$this->getNameComponente('inCodRegimeSelecionados')."','".$this->getNameComponente('inCodRegimeSelecionados')."','".$this->getNameComponente('preencherSubDivisao')."');
                  selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodRegimeSelecionados')."',false);";
    $this->obCmbRegime->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $this->obCmbRegime->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $this->obCmbRegime->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $this->obCmbRegime->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $this->obCmbRegime->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $this->obCmbRegime->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $this->obCmbSubDivisao = new SelectMultiplo();
    $this->obCmbSubDivisao->setName         ( 'inCodSubDivisao'                                             );
    $this->obCmbSubDivisao->setRotulo       ( "Subdivisão"                                                  );
    $this->obCmbSubDivisao->setTitle        ( "Selecione a(s) subdivisão(ões)."                             );
    $this->obCmbSubDivisao->SetNomeLista1   ( $this->getNameComponente('inCodSubDivisaoDisponiveis')        );
    $this->obCmbSubDivisao->setCampoId1     ( '[cod_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setCampoDesc1   ( '[nom_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setStyle1       ( "width: 300px"                                                );
    $this->obCmbSubDivisao->SetRecord1      ( new recordset                                                 );
    $this->obCmbSubDivisao->SetNomeLista2   ( $this->getNameComponente('inCodSubDivisaoSelecionados')       );
    $this->obCmbSubDivisao->setCampoId2     ( '[cod_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setCampoDesc2   ( '[nom_sub_divisao]'                                           );
    $this->obCmbSubDivisao->setStyle2       ( "width: 300px"                                                );
    $this->obCmbSubDivisao->SetRecord2      ( $rsSubDivisao                                                 );
    $stOnClick = "if (document.frm.inCodFuncaoSelecionados) { selecionarSelectMultiploRegSubCarEsp('inCodFuncaoSelecionados',true); }
                  if (document.frm.inCodCargoSelecionados) { selecionarSelectMultiploRegSubCarEsp('inCodCargoSelecionados',true);  }
                  if (document.frm.".$this->getNameComponente('inCodEspecialidadeSelecionados').") { selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodEspecialidadeSelecionados')."',true); }
                  selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodSubDivisaoSelecionados')."',true);
                  buscaValorBscInner('".CAM_GRH_PES_PROCESSAMENTO."OCFiltroMultiploRegSubCarEsp.php?".Sessao::getId()."','frm','".$this->getNameComponente('inCodCargoSelecionados')."','".$this->getNameComponente('inCodCargoSelecionados')."','".$this->getNameComponente('preencherCargo')."');
                  selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodSubDivisaoSelecionados')."',false);";
    $this->obCmbSubDivisao->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $this->obCmbSubDivisao->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $this->obCmbSubDivisao->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $this->obCmbSubDivisao->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $this->obCmbSubDivisao->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $this->obCmbSubDivisao->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $this->obCmbCargo = new SelectMultiplo();
    $this->obCmbCargo->setName              ( 'inCodCargo'                                                  );
    $this->obCmbCargo->setRotulo            ( "Cargo"                                                       );
    $this->obCmbCargo->setTitle             ( "Selecione o(s) cargo(s)."                                    );
    $this->obCmbCargo->SetNomeLista1        ( $this->getNameComponente('inCodCargoDisponiveis')             );
    $this->obCmbCargo->setCampoId1          ( '[cod_cargo]'                                                 );
    $this->obCmbCargo->setCampoDesc1        ( '[descr_cargo]'                                               );
    $this->obCmbCargo->setStyle1            ( "width: 300px"                                                );
    $this->obCmbCargo->SetRecord1           (  new recordset                                                );
    $this->obCmbCargo->SetNomeLista2        ( $this->getNameComponente('inCodCargoSelecionados')            );
    $this->obCmbCargo->setCampoId2          ( '[cod_cargo]'                                                 );
    $this->obCmbCargo->setCampoDesc2        ( '[descr_cargo]'                                               );
    $this->obCmbCargo->setStyle2            ( "width: 300px"                                                );
    $this->obCmbCargo->SetRecord2           ( $rsCargo                                                 );
    $stOnClick = "selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodEspecialidadeSelecionados')."',true);
                  selecionarSelectMultiploRegSubCarEsp('inCodCargoSelecionados',true);
                  buscaValorBscInner('".CAM_GRH_PES_PROCESSAMENTO."OCFiltroMultiploRegSubCarEsp.php?".Sessao::getId()."','frm','inCodCargoSelecionados','inCodCargoSelecionados','preencherEspecialidade');
                  selecionarSelectMultiploRegSubCarEsp('inCodCargoSelecionados',false);";
    $this->obCmbCargo->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $this->obCmbCargo->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $this->obCmbCargo->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $this->obCmbCargo->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $this->obCmbCargo->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $this->obCmbCargo->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $this->obCmbFuncao = new SelectMultiplo();
    $this->obCmbFuncao->setName              ( 'inCodFuncao'                                                 );
    $this->obCmbFuncao->setRotulo            ( "Função"                                                      );
    $this->obCmbFuncao->setTitle             ( "Selecione a(s) função(ões)."                                 );
    $this->obCmbFuncao->SetNomeLista1        ( 'inCodFuncaoDisponiveis'                                      );
    $this->obCmbFuncao->setCampoId1          ( '[cod_cargo]'                                                );
    $this->obCmbFuncao->setCampoDesc1        ( '[descr_cargo]'                                                 );
    $this->obCmbFuncao->setStyle1            ( "width: 300px"                                                );
    $this->obCmbFuncao->SetRecord1           ( new recordset                                                 );
    $this->obCmbFuncao->SetNomeLista2        ( 'inCodFuncaoSelecionados'                                     );
    $this->obCmbFuncao->setCampoId2          ( '[cod_cargo]'                                                );
    $this->obCmbFuncao->setCampoDesc2        ( '[descr_cargo]'                                                 );
    $this->obCmbFuncao->setStyle2            ( "width: 300px"                                                );
    $this->obCmbFuncao->SetRecord2           ( $rsCargo                                                 );
    $stOnClick = "selecionarSelectMultiploRegSubCarEsp('".$this->getNameComponente('inCodEspecialidadeSelecionados')."',true);
                  selecionarSelectMultiploRegSubCarEsp('inCodFuncaoSelecionados',true);
                  buscaValorBscInner('".CAM_GRH_PES_PROCESSAMENTO."OCFiltroMultiploRegSubCarEsp.php?".Sessao::getId()."','frm','inCodFuncaoSelecionados','inCodFuncaoSelecionados','preencherEspecialidadeFunc');
                  selecionarSelectMultiploRegSubCarEsp('inCodFuncaoSelecionados',false);";
    $this->obCmbFuncao->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $this->obCmbFuncao->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $this->obCmbFuncao->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $this->obCmbFuncao->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $this->obCmbFuncao->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $this->obCmbFuncao->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $this->obCmbEspecialidade = new SelectMultiplo();
    $this->obCmbEspecialidade->setName      ( 'inCodEspecialidade'                                          );
    $this->obCmbEspecialidade->setRotulo    ( "Especialidade"                                               );
    $this->obCmbEspecialidade->setTitle     ( "Selecione a(s) especialidade(s)."                            );
    $this->obCmbEspecialidade->SetNomeLista1( $this->getNameComponente('inCodEspecialidadeDisponiveis')     );
    $this->obCmbEspecialidade->setCampoId1  ( '[cod_especialidade]'                                         );
    $this->obCmbEspecialidade->setCampoDesc1( '[descr_espec]'                                               );
    $this->obCmbEspecialidade->setStyle1    ( "width: 300px"                                                );
    $this->obCmbEspecialidade->SetRecord1   ( new recordset                                                 );
    $this->obCmbEspecialidade->SetNomeLista2( $this->getNameComponente('inCodEspecialidadeSelecionados')    );
    $this->obCmbEspecialidade->setCampoId2  ( '[cod_especialidade]'                                         );
    $this->obCmbEspecialidade->setCampoDesc2( '[descr_espec]'                                               );
    $this->obCmbEspecialidade->setStyle2    ( "width: 300px"                                                );
    $this->obCmbEspecialidade->SetRecord2   ( $rsEspecialidades                                             );

}

function getNameComponente($stName)
{
    if ( $this->getFuncao() ) {
        return $stName."Func";
    } else {
        return $stName;
    }
}

/**
    * Monta os combos de competencia
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponente    ( $this->obCmbRegime                );
    $obFormulario->addComponente    ( $this->obCmbSubDivisao            );
    if ( $this->getFuncao() ) {
        if ( !$this->getDisabledFuncao() ) {
            $obFormulario->addComponente( $this->obCmbFuncao                );
        }
    } else {
        if ( !$this->getDisabledCargo() ) {
            $obFormulario->addComponente( $this->obCmbCargo                 );
        }
    }
    if ( !$this->getDisabledEspecialidade() ) {
        $obFormulario->addComponente    ( $this->obCmbEspecialidade         );
    }
}

/**
    *Função que desabilita o combo informado e os posteriores a ele
    * @access Public
    * @param String
*/
function disabledFuncao()
{
    $this->setDisabledFuncao(true);
    $this->disabledEspecialidade();
}

/**
    *Função que desabilita o combo informado e os posteriores a ele
    * @access Public
    * @param String
*/
function disabledCargo()
{
    $this->setDisabledFuncao(true);
    $this->disabledEspecialidade();
}

/**
    *Função que desabilita o combo informado e os posteriores a ele
    * @access Public
    * @param String
*/
function disabledEspecialidade()
{
    $this->setDisabledEspecialidade(true);
}

/**
    *Função que desabilita o combo informado e os posteriores a ele
    * @access Public
    * @param String
*/
function setNull($boNull)
{
    $this->obCmbRegime->setNull($boNull);
    $this->obCmbSubDivisao->setNull($boNull);
    if ($this->getFuncao()) {
        $this->obCmbFuncao->setNull($boNull);
    } else {
        $this->obCmbCargo->setNull($boNull);
    }
}

}

?>
