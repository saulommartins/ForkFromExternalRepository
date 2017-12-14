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
    * Página de
    * Data de criação : 03/11/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.02.11

    $Id: RPatrimonioRelatorioFichaPatrimonial.class.php 59612 2014-09-02 12:00:51Z gelson $
**/

set_time_limit(0);
ini_set('memory_limit','512M');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                                              );
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                      );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php");
include_once( CAM_GP_PAT_NEGOCIO."RPatrimonioAtributoPatrimonio.class.php");

class RPatrimonioRelatorioFichaPatrimonial extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioNatureza;
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioGrupo;
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioEspecie;
/**
    * @var Object
    * @access Private
*/
var $inCodNatureza;
/**
    * @var Object
    * @access Private
*/

var $inCodGrupo;
/**
    * @var Object
    * @access Private
*/

var $inCodOrgao;
/**
    * @var Object
    * @access Private
*/

var $inCodUnidade;
/**
    * @var Object
    * @access Private
*/

var $inCodDepartamento;
/**
    * @var Object
    * @access Private
*/

var $inCodSetor;
/**
    * @var Object
    * @access Private
*/

var $inCodLocal;
/**
    * @var Object
    * @access Private
*/
var $inCodEspecie;
/**
    * @var Object
    * @access Private
*/

var $obRRelatorio;

/**
    * @var Object
    * @access Private
*/
var $obTPatrimononioBem;
/**
    * @var Object
    * @access Private
*/
var $obRPatrimonioRelatorioFichaPatrimonial;
/**
    * @var Object
    * @access Private
*/
var $stTipoRelatorio;
/**
    * @var Object
    * @access Private
*/
var $stHistorico;

/**
    * @var Object
    * @access Private
*/
var $inCodBemInicial;

/**
    * @var Object
    * @access Private
*/
var $inCodBemFinal;

/**
    * @var Integer
    * @access Private
*/
var $inCodFornecedor;

/**
    * @var Object
    * @access Private
*/
var $maxAtributo;
/**
    * @var Object
    * @access Private
*/
var $stDataInicial;
/**
    * @var Object
    * @access Private
*/
var $stDataFinal;
/**
    * @var Object
    * @access Private
*/

var $stDataInicialIncorporacao;
/**
    * @var Object
    * @access Private
*/
var $stDataFinalIncorporacao;
/**
    * @var Object
    * @access Private
*/

var $boQuebraPagina;

/**
     * @access Public
     * @param Object $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/

function setHistorico($valor) { $this->stHistorico      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setMaxAtributo($valor) { $this->inMaxAtributo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodBemInicial($valor) { $this->inCodBemInicial      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodBemFinal($valor) { $this->inCodBemFinal      = $valor; }

/**
     * @access Public
     * @param Integer $valor
*/
function setCodFornecedor($valor) { $this->inCodFornecedor      = $valor; }

/**
     * @access Public
     * @return Object
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;                }

/**
     * @access Public
     * @return Object
*/
function getHistorico() { return $this->stHistorico;                }
/**
     * @access Public
     * @return Object
*/
function getMaxAtributo() { return $this->inMaxAtributo;                }

/**
     * @access Public
     * @return Object
*/
function getCodBemInicial() { return $this->stDataInicial;                }
/**
     * @access Public
     * @return Object
*/
function getCodBemFinal() { return $this->stDataFinal;                }
/**
/**
     * @access Public
     * @param Object $valor
*/
function setCodNatureza($valor) { $this->inCodNatureza      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodGrupo($valor) { $this->inCodGrupo      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodOrgao($valor) { $this->inCodOrgao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodUnidade($valor) { $this->inCodUnidade      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodDepartamento($valor) { $this->inCodDepartamento      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodSetor($valor) { $this->inCodSetor      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodLocal($valor) { $this->inCodLocal      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEspecie($valor) { $this->inCodEspecie     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataInicialIncorporacao($valor) { $this->stDataInicialIncorporacao     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setDataFinalIncorporacao($valor) { $this->stDataFinalIncorporacao     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setQuebraPagina($valor) { $this->boQuebraPagina     = $valor; }
/*
     * @access Public
     * @return Object
*/
function getCodNatureza() { return $this->inCodNatureza;                }
/**
     * @access Public
     * @return Object
*/
function getCodGrupo() { return $this->inCodGrupo;                }
/**
     * @access Public
     * @return Object
*/
function getCodOrgao() { return $this->inCodOrgao;                }
/**
     * @access Public
     * @return Object
*/
function getCodUnidade() { return $this->inCodUnidade;                }
/**
     * @access Public
     * @return Object
*/
function getCodDepartamento() { return $this->inCodDepartamento;                }
/**
     * @access Public
     * @return Object
*/
function getCodSetor() { return $this->inCodSetor;                }
/**
     * @access Public
     * @return Object
*/
function getCodLocal() { return $this->inCodLocal;                }
/**
     * @access Public
     * @return Object
*/
function getCodEspecie() { return $this->inCodEspecie;                }
/**
     * @access Public
     * @return Object
*/
function getDataInicial() { return $this->stDataInicial;              }
/**
     * @access Public
     * @return Object
*/
function getDataFinal() { return $this->stDataFinal;                }
/**
     * @access Public
     * @return Object
*/
function getDataInicialIncorporacao() { return $this->stDataInicialIncorporacao;              }
/**
     * @access Public
     * @return Object
*/
function getDataFinalIncorporacao() { return $this->stDataFinalIncorporacao;                }
/**
     * @access Public
     * @return Object
*/
function getQuebraPagina() { return $this->boQuebraPagina;                }
/**
    * Método Construtor
    * @access Private
*/

function RPatrimonioRelatorioFichaPatrimonial()
{
    $this->obRRelatorio                     = new RRelatorio;
    $this->obTPatrimononioBem               = new TPatrimonioBem;
}
/**
    * Método abstrato
    * @access Public
*/
function GeraRecordSet(&$arRs, $stFiltro="", $boTransacao="")
{
$this->obTPatrimononioBem->setDado("inCodNatureza", $this->inCodNatureza);
$this->obTPatrimononioBem->setDado("inCodGrupo",    $this->inCodGrupo);
$this->obTPatrimononioBem->setDado("inCodEspecie",  $this->inCodEspecie);
$this->obTPatrimononioBem->setDado("inCodBemInicial"  , $this->inCodBemInicial);
$this->obTPatrimononioBem->setDado("inCodBemFinal"   , $this->inCodBemFinal);
$this->obTPatrimononioBem->setDado("inCodFornecedor", $this->inCodFornecedor);
$this->obTPatrimononioBem->setDado("inCodOrgao"   , $this->inCodOrgao);

$this->obTPatrimononioBem->setDado("inCodLocal"   , $this->inCodLocal);
$this->obTPatrimononioBem->setDado("stDataInicial" , $this->stDataInicial);
$this->obTPatrimononioBem->setDado("stDataFinal"   , $this->stDataFinal);
$this->obTPatrimononioBem->setDado("stDataInicialIncorporacao" , $this->stDataInicialIncorporacao);
$this->obTPatrimononioBem->setDado("stDataFinalIncorporacao"   , $this->stDataFinalIncorporacao);

if ($this->stTipoRelatorio == "completo") {
    if ($this->stHistorico == 'não') {
        $obErro = $this->obTPatrimononioBem->recuperaFichaPatrimonialCompleta($rsRecordSet,$stFiltro,$boTransacao);
    } else {
        $obErro = $this->obTPatrimononioBem->recuperaFichaPatrimonialCompletaHistorico($rsRecordSet,$stFiltro,$boTransacao);
    }
}

if ($this->stTipoRelatorio == "resumido") {
    if ($this->stHistorico == 'não') {
        $obErro = $this->obTPatrimononioBem->recuperaFichaPatrimonialResumida($rsRecordSet,$stFiltro,$boTransacao);
    } else {
        $obErro = $this->obTPatrimononioBem->recuperaFichaPatrimonialResumidaHistorico($rsRecordSet,$stFiltro,$boTransacao);
    }
}

$atributos = new RPatrimonioAtributoPatrimonio;
$atributos->listar($rsAtributos);
while (!$rsAtributos->eof()) {
    $arrayNomeAtributos["nomeAtributo"][$rsAtributos->getCampo("cod_atributo")] = $rsAtributos->getCampo("nom_atributo");
    $rsAtributos->proximo();
}

if ( !$obErro->ocorreu()) {
    if ($this->stTipoRelatorio == "resumido") {
        $arRecordSet = array();
        $inCount = 0;

        while (!$rsRecordSet->eof()) {
            $codBem  = $rsRecordSet->getCampo("cod_bem");
            $timestamp = $rsRecordSet->getCampo("timestamp");
            if (($codBem == $codBemAnterior && $timestamp != $timestampAnterior) || ($codBem != $codBemAnterior) ||($this->stTipoRelatorio == "resumido") || ($stHistorico == 'não') ) {
                $arRecordSet[$inCount]['codigo'       ] = $rsRecordSet->getCampo('cod_bem'      );
                $arRecordSet[$inCount]['nom_cgm'      ] = $rsRecordSet->getCampo('nom_cgm');
                $arRecordSet[$inCount]['placa'        ] = $rsRecordSet->getCampo('num_placa'    );
                $arRecordSet[$inCount]['descricao'    ] = str_replace(chr(13),' ',$rsRecordSet->getCampo('detalhamento' ));
                $arRecordSet[$inCount]['especie'      ] = $rsRecordSet->getCampo('nom_especie'  );
                $arRecordSet[$inCount]['local'        ] = $rsRecordSet->getCampo('nom_local'    );
                $arRecordSet[$inCount]['classificacao'] = $rsRecordSet->getCampo('classificacao');
                $arRecordSet[$inCount]['natureza'     ] = $rsRecordSet->getCampo('nom_natureza' );
                $arRecordSet[$inCount]['grupo'        ] = $rsRecordSet->getCampo('nom_grupo'    );
                $arRecordSet[$inCount]['nota_fiscal'  ] = $rsRecordSet->getCampo('nota_fiscal'  );
                $data = "";
                $data =  explode('-',$rsRecordSet->getCampo('dt_aquisicao') );
                $arRecordSet[$inCount]['dt_aquisicao'    ] =  $data[2]."/".$data[1]."/".$data[0];//aquisicao
                $data = "";
                $data =  explode('-',$rsRecordSet->getCampo('dt_incorporacao') );
                $arRecordSet[$inCount]['dt_incorporacao'    ] =  $data[2]."/".$data[1]."/".$data[0];//incorporacao
                $arRecordSet[$inCount]['vl_bem'          ] = $rsRecordSet->getCampo('vl_bem'          );//valor bem
                $data = $rsRecordSet->getCampo('timestamp'       );
                $data = explode (" ",$data);
                $time = $data[1];
                $time = explode (".",$time);
                $time = $time[0];
                $data = explode ("-",$data[0]);
                $data = $data[2]."/".$data[1]."/".$data[0]." ".$time;
                $arRecordSet[$inCount]['timestamp'       ] = $data;
                $inCount++;
            }
            $codBemAnterior = $rsRecordSet->getCampo("cod_bem");
            $timestampAnterior = $rsRecordSet->getCampo("timestamp");
            $rsRecordSet->proximo();
        }
        $inCountNovo = 0;
        $inCountAux = 0;
        $inCountAt = 0;
        $rsRecordSet->setPrimeiroElemento();
        $codBemAnterior = 0;
        while ($inCount != $inCountAux) {
            $codBem = $arRecordSet[$inCountAux]['codigo'];
            if ($codBemAnterior != $codBem) {
                $arNovo[$inCountNovo]["descricao"]= "CÓDIGO";
                $arNovo[$inCountNovo]["valor"    ] =  $arRecordSet[$inCountAux]['codigo'];
                $arNovo[$inCountNovo]["classificacao"] =  $arRecordSet[$inCountAux]['classificacao'];
                $arNovo[$inCountNovo]["especie"      ] =  $arRecordSet[$inCountAux]['especie'];
                $arNovo[$inCountNovo]["natureza"     ] =  $arRecordSet[$inCountAux]['natureza'];
                $arNovo[$inCountNovo]["grupo"        ] =  $arRecordSet[$inCountAux]['grupo'];
                $inCountNovo++;
                $arNovo[$inCountNovo]["descricao"] = "PLACA";
                $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["placa"];
                $inCountNovo++;
                if ($this->inCodEspecie < 1) {
                    $arNovo[$inCountNovo]["descricao"] = "NATUREZA";
                    $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["natureza"];
                    $inCountNovo++;
                    $arNovo[$inCountNovo]["descricao"] = "GRUPO";
                    $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["grupo"];
                    $inCountNovo++;
                    $arNovo[$inCountNovo]["descricao"] = "ESPECIE";
                    $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["especie"];
                    $inCountNovo++;
                }

                //QUEBRA DE LINHA
               $stNomContaTemp = str_replace( chr(10), "", $arRecordSet[$inCountAux]['descricao'] );
               $stNomContaTemp = wordwrap( $stNomContaTemp,80,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
               $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores

                $arNovo[$inCountNovo]["descricao"] = "DESCRIÇÃO";
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arNovo[$inCountNovo]["valor"    ] = $stNomContaTemp;
                    $inCountNovo++;
                }

                $arNovo[$inCountNovo]["descricao"] = "NOTA FISCAL";
                $arNovo[$inCountNovo]["valor"    ] = $arRecordSet[$inCountAux]["nota_fiscal"];
                $inCountNovo++;
            }
            if ($this->stTipoRelatorio == 'resumido') {
                $arNovo[$inCountNovo]['descricao'] = "DATA/HORA";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["timestamp"];
                $inCountNovo++;
            }
            if ($codBemAnterior != $codBem) {
                $arNovo[$inCountNovo]['descricao'] = "FORNECEDOR";
                $arNovo[$inCountNovo]['valor'    ] = $arRecordSet[$inCountAux]["nom_cgm"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "EMPENHO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["num_empenho"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "DATA DA AQUISIÇÃO";
                if ($arRecordSet[$inCountAux]["dt_aquisicao"] != "//") {
                    $arNovo[$inCountNovo]['valor'    ] =   $arRecordSet[$inCountAux]["dt_aquisicao"];
                }
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "DATA DE INCORPORAÇÃO";
                if ($arRecordSet[$inCountAux]["dt_incorporacao"] != "//") {
                    $arNovo[$inCountNovo]['valor'    ] =   $arRecordSet[$inCountAux]["dt_incorporacao"];
                }
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "VALOR BEM";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["vl_bem"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "DATA DE GARANTIA";
                if ($arRecordSet[$inCountAux]["dt_garantia"] != "//") {
                    $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["dt_garantia"];
                }
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "IDENTIFICAÇÃO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["identificacao"];
                $inCountNovo++;
            }
            $codBemAnterior = $arRecordSet[$inCountAux]['codigo'];
            $arNovo[$inCountNovo]['descricao'] = "LOCAL";
            $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["local"];
            $inCountNovo++;
            if ($this->stTipoRelatorio == "completo") {
                $arNovo[$inCountNovo]['descricao'] = "ORGÃO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["nom_orgao"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "SITUAÇÃO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["nom_situacao"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "DESCRIÇÃO DA SITUAÇÃO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["desc_situacao"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "DATA/HORA";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["timestamp"];
                $inCountNovo++;
                //Atributos dinâmicos
                while ($maxAtributo != $inCountAt) {
                    if ( $arRecordSet[$inCountAux][$arAtExistentes[$inCountAt]['cod_atributo']] != trim("")) {
                        $arNovo[$inCountNovo]["descricao"] = strtoupper($arAtExistentes[$inCountAt]["nom_atributo"]);
                        $arNovo[$inCountNovo]['valor'] = $arRecordSet[$inCountAux][$arAtExistentes[$inCountAt]['cod_atributo']] ;
                        $inCountNovo++;
                    }
                    $inCountAt++;
                }
                $inCountAt = 0;
            }
            //quebra de linhas para cada código de bem...
            $arNovo[$inCountNovo]['descricao'  ] = "----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
            $arNovo[$inCountNovo]['valor'      ] = "-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
            $inCountNovo++;
            $arNovo[$inCountNovo]['descricao'  ] = "";
            $arNovo[$inCountNovo]['valor'      ] = "";
            $inCountNovo++;
            $inCountAux++;
            $codBemAnteriorTeste = $rsRecordSet->getCampo("cod_bem");
            $rsRecordSet->proximo();
            $codBem = $rsRecordSet->getCampo("cod_bem");
            if ($codBemAnteriorTeste != $codBem) {
                if ($this->boQuebraPagina and $inCountNovo > 0) {
                    $arNovo[$inCountNovo]['boQuebraPagina'] = true;
                    $inCountNovo++;
                }
            }
        }
    }
    if ($this->stTipoRelatorio == "completo") {
        $arRecordSet = array();
        $inCount = 0;
        while (!$rsRecordSet->eof()) {
            $codBem  = $rsRecordSet->getCampo("cod_bem");
            $timestamp = $rsRecordSet->getCampo("timestamp");
            if (($codBem == $codBemAnterior && $timestamp != $timestampAnterior) || ($codBem != $codBemAnterior) ||($this->stTipoRelatorio == "resumido") || ($stHistorico == 'não') ) {
                $arRecordSet[$inCount]['codigo'       ] = $rsRecordSet->getCampo('cod_bem'      );
                $arRecordSet[$inCount]['placa'        ] = $rsRecordSet->getCampo('num_placa'    );
                $arRecordSet[$inCount]['descricao'    ] = str_replace(chr(13),' ',$rsRecordSet->getCampo('detalhamento' ));
                $arRecordSet[$inCount]['especie'      ] = $rsRecordSet->getCampo('nom_especie'  );
                $arRecordSet[$inCount]['local'        ] = $rsRecordSet->getCampo('nom_local'    );
                $arRecordSet[$inCount]['classificacao'] = $rsRecordSet->getCampo('classificacao');
                $arRecordSet[$inCount]['natureza'     ] = $rsRecordSet->getCampo('nom_natureza' );
                $arRecordSet[$inCount]['grupo'        ] = $rsRecordSet->getCampo('nom_grupo'    );
                $arRecordSet[$inCount]['nota_fiscal'  ] = $rsRecordSet->getCampo('nota_fiscal'  );
                $arRecordSet[$inCount]['nom_cgm'      ] = $rsRecordSet->getCampo('nom_cgm'         );//cgm
                $arRecordSet[$inCount]['num_empenho'  ] = $rsRecordSet->getCampo('num_empenho'     );//empenho
                if ($this->stHistorico == 'não') {
                $data = "";
                $data =  explode('/',$rsRecordSet->getCampo('dt_aquisicao') );
                $arRecordSet[$inCount]['dt_aquisicao'    ] =  $data[0]."/".$data[1]."/".$data[2];//aquisicao
                $data = "";
                $data =  explode('/',$rsRecordSet->getCampo('dt_incorporacao') );
                $arRecordSet[$inCount]['dt_incorporacao'    ] =  $data[0]."/".$data[1]."/".$data[2];//incorporacao
                } else {
                $data = "";
                $data =  explode('-',$rsRecordSet->getCampo('dt_aquisicao') );
                $arRecordSet[$inCount]['dt_aquisicao'    ] =  $data[2]."/".$data[1]."/".$data[0];//aquisicao
                $data = "";
                $data =  explode('-',$rsRecordSet->getCampo('dt_incorporacao') );
                $arRecordSet[$inCount]['dt_incorporacao'    ] =  $data[2]."/".$data[1]."/".$data[0];//incorporacao
                }
                $arRecordSet[$inCount]['vl_bem'          ] = $rsRecordSet->getCampo('vl_bem'          );//valor bem
                $data = "";
                $data =  explode('-',$rsRecordSet->getCampo('dt_garantia') );
                $arRecordSet[$inCount]['dt_garantia'     ] =  $data[2]."/".$data[1]."/".$data[0];//garantia

                if ($identificacao == 'T' || $identificacao == 't') {
                    $identificacao = "sim";
                } else {
                    $identificacao = "não";
                }
                $arRecordSet[$inCount]['identificacao'   ] = $identificacao;//identificacao
                $arRecordSet[$inCount]['nom_orgao'       ] = $rsRecordSet->getCampo('nom_orgao'       );//orgao
                $arRecordSet[$inCount]['nom_situacao'    ] = $rsRecordSet->getCampo('nom_situacao'    );//situacao
                $data = $rsRecordSet->getCampo('timestamp'       );
                $data = explode (" ",$data);
                $time = $data[1];
                $time = explode (".",$time);
                $time = $time[0];
                $data = explode ("-",$data[0]);
                $data = $data[2]."/".$data[1]."/".$data[0]." ".$time;
                $arRecordSet[$inCount]['timestamp'       ] = $data;
                $arRecordSet[$inCount]['desc_situacao'   ] = $rsRecordSet->getCampo('desc_situacao'   );//descrição histórico
                $codBemAnterior = $rsRecordSet->getCampo('cod_bem');
                $timestampAnterior = $rsRecordSet->getCampo('timestamp');
                $inCountAtributos = 0;
                $inCount++;
            }
            $codBemAnterior = $rsRecordSet->getCampo("cod_bem");
            $timestampAnterior = $rsRecordSet->getCampo("timestamp");
            $rsRecordSet->proximo();
        }
        $inCountNovo = 0;
        $inCountAux = 0;
        $inCountAt = 0;
        $rsRecordSet->setPrimeiroElemento();
        $codBemAnterior = 0;
        while ($inCount != $inCountAux) {
            $quebra =  "true";
            $codBem = $arRecordSet[$inCountAux]['codigo'];
            if ($codBemAnterior != $codBem) {
                $arNovo[$inCountNovo]["descricao"]= "CÓDIGO";
                $arNovo[$inCountNovo]["valor"    ] =  $arRecordSet[$inCountAux]['codigo'];
                $arNovo[$inCountNovo]["classificacao"] =  $arRecordSet[$inCountAux]['classificacao'];
                $arNovo[$inCountNovo]["especie"      ] =  $arRecordSet[$inCountAux]['especie'];
                $arNovo[$inCountNovo]["natureza"     ] =  $arRecordSet[$inCountAux]['natureza'];
                $arNovo[$inCountNovo]["grupo"        ] =  $arRecordSet[$inCountAux]['grupo'];
                $inCountNovo++;
                $arNovo[$inCountNovo]["descricao"] = "PLACA";
                $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["placa"];
                $inCountNovo++;
                if ($this->inCodEspecie < 1) {
                    $arNovo[$inCountNovo]["descricao"] = "NATUREZA";
                    $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["natureza"];
                    $inCountNovo++;
                    $arNovo[$inCountNovo]["descricao"] = "GRUPO";
                    $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["grupo"];
                    $inCountNovo++;
                    $arNovo[$inCountNovo]["descricao"] = "ESPECIE";
                    $arNovo[$inCountNovo]["valor"    ]  = $arRecordSet[$inCountAux]["especie"];
                    $inCountNovo++;
                }

                //QUEBRA DE LINHA
               $stNomContaTemp = str_replace( chr(10), "", $arRecordSet[$inCountAux]['descricao'] );
               $stNomContaTemp = wordwrap( $stNomContaTemp,80,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
               $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores

                $arNovo[$inCountNovo]["descricao"] = "DESCRIÇÃO";
                foreach ($arNomContaOLD as $stNomContaTemp) {
                    $arNovo[$inCountNovo]["valor"    ] = $stNomContaTemp;
                    $inCountNovo++;
                }

                $arNovo[$inCountNovo]["descricao"] = "NOTA FISCAL";
                $arNovo[$inCountNovo]["valor"    ] = $arRecordSet[$inCountAux]["nota_fiscal"];
                $inCountNovo++;
            }
            if ($this->stTipoRelatorio == 'resumido') {
                $arNovo[$inCountNovo]['descricao'] = "DATA/HORA";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["timestamp"];
                $inCountNovo++;
            }
            if ($codBemAnterior != $codBem) {
                $quebra =  "true";
                if ($this->stTipoRelatorio == "completo") {
                    $arNovo[$inCountNovo]['descricao'] = "FORNECEDOR";
                    $arNovo[$inCountNovo]['valor'    ] = $arRecordSet[$inCountAux]["nom_cgm"];
                    $inCountNovo++;
                    $arNovo[$inCountNovo]['descricao'] = "EMPENHO";
                    $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["num_empenho"];
                    $inCountNovo++;
                    $arNovo[$inCountNovo]['descricao'] = "DATA DA AQUISIÇÃO";
                    if ($arRecordSet[$inCountAux]["dt_aquisicao"] != "//") {
                        $arNovo[$inCountNovo]['valor'    ] =   $arRecordSet[$inCountAux]["dt_aquisicao"];
                    }
                    $inCountNovo++;
                    $arNovo[$inCountNovo]['descricao'] = "DATA DE INCORPORAÇÃO";
                    if ($arRecordSet[$inCountAux]["dt_incorporacao"] != "//") {
                        $arNovo[$inCountNovo]['valor'    ] =   $arRecordSet[$inCountAux]["dt_incorporacao"];
                    }
                    $inCountNovo++;
                    $arNovo[$inCountNovo]['descricao'] = "VALOR BEM";
                    $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["vl_bem"];
                    $inCountNovo++;
                    $arNovo[$inCountNovo]['descricao'] = "DATA DE GARANTIA";
                    if ($arRecordSet[$inCountAux]["dt_garantia"] != "//") {
                        $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["dt_garantia"];
                    }
                    $inCountNovo++;
                    $arNovo[$inCountNovo]['descricao'] = "IDENTIFICAÇÃO";
                    $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["identificacao"];
                    $inCountNovo++;
                }
            } else {
                $quebra == "false";
            }
            $codBemAnterior = $arRecordSet[$inCountAux]['codigo'];
            $arNovo[$inCountNovo]['descricao'] = "LOCAL";
            $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["local"];
            $inCountNovo++;
            if ($this->stTipoRelatorio == "completo") {
                $arNovo[$inCountNovo]['descricao'] = "ORGÃO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["nom_orgao"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "SITUAÇÃO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["nom_situacao"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "DESCRIÇÃO DA SITUAÇÃO";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["desc_situacao"];
                $inCountNovo++;
                $arNovo[$inCountNovo]['descricao'] = "DATA/HORA";
                $arNovo[$inCountNovo]['valor'    ] =  $arRecordSet[$inCountAux]["timestamp"];
                $inCountNovo++;
                //Atributos dinâmicos
                while ($maxAtributo != $inCountAt) {
                    if ( $arRecordSet[$inCountAux][$arAtExistentes[$inCountAt]['cod_atributo']] != trim("")) {
                        $arNovo[$inCountNovo]["descricao"] = strtoupper($arAtExistentes[$inCountAt]["nom_atributo"]);
                        $arNovo[$inCountNovo]['valor'] = $arRecordSet[$inCountAux][$arAtExistentes[$inCountAt]['cod_atributo']] ;
                        $inCountNovo++;
                    }
                    $inCountAt++;
                }
                $inCountAt = 0;
            }
            //quebra de linhas para cada código de bem...
            $arNovo[$inCountNovo]['descricao'  ] = "----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
            $arNovo[$inCountNovo]['valor'      ] = "-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
            $inCountNovo++;
            $arNovo[$inCountNovo]['descricao'  ] = "";
            $arNovo[$inCountNovo]['valor'      ] = "";
            $inCountNovo++;
            $inCountAux++;
            $codBemAnteriorTeste = $rsRecordSet->getCampo("cod_bem");
            $rsRecordSet->proximo();
            $codBem = $rsRecordSet->getCampo("cod_bem");
            if ($quebra == "true") {
                if ($this->boQuebraPagina and $inCountNovo > 0) {
                    if ($codBemAnteriorTeste != $codBem) {
                        $arNovo[$inCountNovo]['boQuebraPagina'] = true;
                        $inCountNovo++;
                    }
                }
            }
        }

    }  //fim do completo
}//fim do $obErro

if ($this->boQuebraPagina == 'true') {
    $inCountArray = 0;
    $inCountLinha = 0;
    foreach ($arNovo as $arLinha) {
        if ($arLinha['boQuebraPagina']) {
            $rsRecordSet = new RecordSet();

            if (is_array($arRecord)) {
                $rsRecordSet->preenche( $arRecord );
            } else {
                $rsRecordSet->preenche( array() );
            }
            $arRs[$inCountArray] = $rsRecordSet;
            $inCountArray++;
            $inCountLinha = 0;
            $arRecord = array();
         } else {
            $arRecord[$inCountLinha] = $arLinha;
            $inCountLinha++;
        }
        $rsRecordSet = new RecordSet();
        $rsRecordSet->preenche( $arRecord );
        $arRs[$inCountArray] = $rsRecordSet;
    }
} else {

    $rsRecordSet->addFormatacao("vl_bem","NUMERIC_BR_NULL");
    $rsRecordSet = new RecordSet;
    $rsColunaAtributos = new RecordSet;

    if (is_array($arNovo)) {
        $rsRecordSet->preenche ($arNovo);
    } else {
        $rsRecordSet->preenche (array());
    }
    $arRs = array( $rsRecordSet );
}
}
}
