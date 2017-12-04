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
  * Data de criação : 16/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    Caso de uso: uc-03.02.10
**/

/*
$Log$
Revision 1.8  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:17  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                                                           );
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                   );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php"                                     );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaManutencao.class.php"                                  );

class RFrotaRelatorioControleQuilometragem extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $inCodVeiculo;
/**
    * @var Object
    * @access Private
*/
var $stPrefixo;
/**
    * @var Object
    * @access Private
*/
var $stPlaca;
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
var $inCodMarca;
/**
    * @var Object
    * @access Private
*/
var $inCodModelo;
/**
    * @var Object
    * @access Private
*/

var $inCodTipoCombustivel;
/**
    * @var Object
    * @access Private
*/
var $inCodTipoVeiculo;
/**
    * @var Object
    * @access Private
*/
var $inCGMResponsavel;
/**
    * @var Object
    * @access Private
*/
var $stNomeCGMResponsavel;
/**
    * @var Object
    * @access Private
*/
var $inCodOrdenacao;
/**
    * @var Object
    * @access Private
*/
var $inCodOrigemVeiculo;
/**
    * @var Object
    * @access Private
*/
var $inCodVeiculoBaixado;
/**
    * @var Object
    * @access Private
*/
var $stMes;
/**
    * @var Object
    * @access Private
*/
var $stNomeMes;
/**
    * @var Object
    * @access Private
*/
var $obTFrotaVeiculo;
/**
    * @var Object
    * @access Private
*/
var $obTFrotaManutencao;

//Setters

/**
     * @access Public
     * @param Object $valor
*/
function setCodVeiculo($valor) { $this->inCodVeiculo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setPrefixo($valor) { $this->stPrefixo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setPlaca($valor) { $this->stPlaca      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setDataInicial($valor) { $this->stDataInicial      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setDataFinal($valor) { $this->stDataFinal      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodMarca($valor) { $this->inCodMarca      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodModelo($valor) { $this->inCodModelo      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodTipoVeiculo($valor) { $this->inCodTipoVeiculo    = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodTipoCombustivel($valor) { $this->inCodTipoCombustivel      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCGMResponsavel($valor) { $this->inCGMResponsavel      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setNomeCGMResponsavel($valor) { $this->stNomeCGMResponsavel      = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodOrdenacao($valor) { $this->inCodOrdenacao    = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodOrigemVeiculo($valor) { $this->inCodOrigemVeiculo    = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setCodVeiculoBaixado($valor) { $this->inCodVeiculoBaixado    = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function setNomeMes($valor) { $this->stNomeMes   = $valor; }

//Getters

/**
     * @access Public
     * @return Object
*/
function getCodVeiculo() { return $this->inCodVeiculo;                }

/**
     * @access Public
     * @return Object
*/
function getPrefixo() { return $this->stPrefixo;                }

/**
     * @access Public
     * @return Object
*/
function getPlaca() { return $this->stPlaca;                }

/**
     * @access Public
     * @return Object
*/
function getDataInicial() { return $this->stDataInicial;                }

/**
     * @access Public
     * @return Object
*/
function getDataFinal() { return $this->stDataFinal;                }

/**
     * @access Public
     * @return Object
*/
function getCodMarca() { return $this->inCodMarca;                }
/**
     * @access Public
     * @return Object
*/
function getCodModelo() { return $this->inCodModelo;                }

/**
     * @access Public
     * @return Object
*/
function getCodTipoVeiculo() { return $this->inCodTipoVeiculo;                }

/**
     * @access Public
     * @return Object
*/
function getCodTipoCombustivel() { return $this->inCodTipoCombustivel;                }

/**
     * @access Public
     * @return Object
*/
function getCodVeiculoBaixado() { return $this->inCodVeiculoBaixado;   }

/**
     * @access Public
     * @return Object
*/
function getCGMResponsavel() { return $this->inCGMResponsavel;                }

/**
     * @access Public
     * @return Object
*/
function getNomeCGMResponsavel() { return $this->stNomeCGMResponsavel;                }

/**
     * @access Public
     * @return Object
*/
function getCodOrdenacao() { return $this->inCodOrdenacao;                }

/**
     * @access Public
     * @return Object
*/
function getCodOrigemVeiculo() { return $this->inCodOrigemVeiculo;                }

/**
     * @access Public
     * @return Object
*/
function getNomeMes() { return $this->stNomeMes; }

function RFrotaRelatorioControleQuilometragem()
{
    $this->obRRelatorio                 = new RRelatorio;
    $this->obTFrotaVeiculo              = new TFrotaVeiculo;
    $this->obTFrotaManutencao           = new TFrotaManutencao;
}

function listarDadosControleQuilometragem(&$rsLista, $stOrder ="" , $boTransacao = "")
{
    $obErro = new Erro;

    if ($this->inCodVeiculo)
        $this->obTFrotaVeiculo->setDado("inCodVeiculo", $this->inCodVeiculo);
    if ($this->stDataFinal) {
        $this->obTFrotaVeiculo->setDado("stDataInicial", $this->stDataInicial);
        $this->obTFrotaVeiculo->setDado("stDataFinal", $this->stDataFinal);
    }
    if ($this->stPlaca) {
        $this->obTFrotaVeiculo->setDado("stPlaca", $this->stPlaca);
    }
    if ($this->stPrefixo)
        $this->obTFrotaVeiculo->setDado("stPrefixo", $this->stPrefixo);
    if ($this->inCodOrdenacao)
        $this->obTFrotaVeiculo->setDado("inCodOrdenacao", $this->inCodOrdenacao);
    if ($this->inCodVeiculoBaixado)
        $this->obTFrotaVeiculo->setDado("inCodVeiculoBaixado", $this->inCodVeiculoBaixado);
    if ($this->inCodOrigemVeiculo)
        $this->obTFrotaVeiculo->setDado("inCodOrigemVeiculo", $this->inCodOrigemVeiculo);
    if ($this->inCodModelo)
        $this->obTFrotaVeiculo->setDado("inCodModelo", $this->inCodModelo);
    if ($this->inCodMarca)
        $this->obTFrotaVeiculo->setDado("inCodMarca", $this->inCodMarca);
    if ($this->inCodTipoCombustivel)
        $this->obTFrotaVeiculo->setDado("inCodTipoCombustivel", $this->inCodTipoCombustivel);
    if ($this->inCodTipoVeiculo)
        $this->obTFrotaVeiculo->setDado("inCodTipoVeiculo", $this->inCodTipoVeiculo);

    $obErro =  $this->obTFrotaVeiculo->recuperaControleQuilometragem($rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    if (!$obErro->ocorreu() ) {
        $inCount = 0;
        while ( !$rsRecordSet->eof() ) {

            //QUEBRA DE LINHA
            $stNomContaTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('situacao') );
            $stNomContaTemp = wordwrap( $stNomContaTemp,15,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
            $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores

            if ($rsRecordSet->getCorrente() == 1) {
                $inCount2 = $inCount;
            }
            //FIM DA QUEBRA DE LINHA
            $arVeiculo[$inCount]['codigo']   = $rsRecordSet->getCampo('codigo');
            $arVeiculo[$inCount]['veiculo'] = $rsRecordSet->getCampo('veiculo');
            $arVeiculo[$inCount]['cilindrada'] =$rsRecordSet->getCampo('cilindrada');
            $arVeiculo[$inCount]['combustivel'] =$rsRecordSet->getCampo('combustivel');
            //Adicionado foreach para fazer a quebra de linha no campo selecionado
            foreach ($arNomContaOLD as $stNomContaTemp) {
                $arVeiculo[$inCount2]['situacao']    = $stNomContaTemp;
                $inCount2++;
            }
            //fim do foreach
            $arVeiculo[$inCount]['km_inicial'] =$rsRecordSet->getCampo('km_inicial');
            $arVeiculo[$inCount]['km_final'] =$rsRecordSet->getCampo('km_final');
            $arVeiculo[$inCount]['quantidade'] =$rsRecordSet->getCampo('quantidade');
            $arVeiculo[$inCount]['unidade_medida'] =$rsRecordSet->getCampo('unidade_medida');
            $arVeiculo[$inCount]['valor'] =$rsRecordSet->getCampo('valor');
            $arVeiculo[$inCount]['valor_medio'] =$rsRecordSet->getCampo('valor_medio');
            $quilometragem =  bcsub($arVeiculo[$inCount]['km_final'],$arVeiculo[$inCount]['km_inicial'],1);
            $arVeiculo[$inCount]['consumo'] = bcdiv($quilometragem,$arVeiculo[$inCount]['quantidade'],1);
            $inCount = $inCount2 - 1;
            $inCount++;
            $rsRecordSet->proximo();
        }
       $rsLista = new RecordSet;
       $rsLista->preenche( $arVeiculo );

    }

    return $obErro;
}

}
