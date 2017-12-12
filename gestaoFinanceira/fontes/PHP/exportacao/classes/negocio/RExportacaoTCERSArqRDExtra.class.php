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
    * Classe de regra de negócio RD Extra
    * Data de Criação: 14/02/2005

    * @author Analista: Diego Barbosa
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra

    $Revision: 32397 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.04
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCERSRDExtra.class.php"    	    );

class RExportacaoTCERSArqRDEXTRA
{
var $arRDExtra;
var $roUltimaRDExtra;
var $obTransacao;
var $obRExportacaoTCERSRDExtra;

//SETTERS
function setRDExtra($valor) { $this->arRDExtra = $valor;          }
function setUltimaRDExtra($valor) { $this->roUltimaRDExtra = $valor;    }
function setExportacaoTCERSRDExtra($valor) { $this->obRExportacaoTCERSRDExtra = $valor;    }

//GETTERS
function getRDExtra() { return $this->arRDExtra;           }
function getUltimaRDExtra() { return $this->roUltimaRDExtra;     }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RExportacaoTCERSArqRDEXTRA()
{
    $this->setExportacaoTCERSRDExtra( new RExportacaoTCERSRDExtra() );
    $this->obTransacao =  new Transacao();

}

function addRDExtra()
{
    $this->arRDExtra[] = new RExportacaoTCERSRDExtra();
    $this->roUltimaRDExtra = &$this->arRDExtra[ count( $this->arRDExtra ) -1 ];
}

function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if (count($this->arRDExtra) > 0) {
            $stComplementoChave = $this->obRExportacaoTCERSRDExtra->obTExportacaoTCERSRDExtra->getComplementoChave();
            $this->obRExportacaoTCERSRDExtra->obTExportacaoTCERSRDExtra->setComplementoChave( "exercicio" );
            $this->obRExportacaoTCERSRDExtra->obTExportacaoTCERSRDExtra->setDado( "exercicio", $this->arRDExtra[0]->getExercicio() );
            $obErro = $this->obRExportacaoTCERSRDExtra->obTExportacaoTCERSRDExtra->exclusao( $boTransacao );
            $this->obRExportacaoTCERSRDExtra->obTExportacaoTCERSRDExtra->setComplementoChave( $stComplementoChave );
        }
        if (!$obErro->ocorreu()) {
            foreach ($this->arRDExtra as $obRExportacaoTCERSRDExtra) {
                $obErro = $obRExportacaoTCERSRDExtra->salvar($boTransacao);
                if($obErro->ocorreu())
                    break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obRExportacaoTCERSUniOrcam );

    return $obErro;
}

function getArrayClassificacao()
{
    $arClassificacao[0]["cod_classificacao"] = "1";
    $arClassificacao[0]["nom_classificacao"] = "Restos a Pagar";
    $arClassificacao[1]["cod_classificacao"] = "2";
    $arClassificacao[1]["nom_classificacao"] = "Serviço da Dívida";
    $arClassificacao[2]["cod_classificacao"] = "3";
    $arClassificacao[2]["nom_classificacao"] = "Depósitos";
    $arClassificacao[3]["cod_classificacao"] = "4";
    $arClassificacao[3]["nom_classificacao"] = "Convênios";
    $arClassificacao[4]["cod_classificacao"] = "5";
    $arClassificacao[4]["nom_classificacao"] = "Débitos de Tesouraria";
    $arClassificacao[5]["cod_classificacao"] = "6";
    $arClassificacao[5]["nom_classificacao"] = "Sentenças Judiciais";
    $arClassificacao[6]["cod_classificacao"] = "7";
    $arClassificacao[6]["nom_classificacao"] = "Outras Operações";

    return $arClassificacao;
}

function listaClassificacao(&$rsClassificacao)
{
//    $arClassificacao[0]["cod_classificacao"] = "1";
//    $arClassificacao[0]["nom_classificacao"] = "01 - Restos a Pagar";
//    $arClassificacao[1]["cod_classificacao"] = "2";
//    $arClassificacao[1]["nom_classificacao"] = "02 - Serviço da Dívida";
//    $arClassificacao[2]["cod_classificacao"] = "3";
//    $arClassificacao[2]["nom_classificacao"] = "03 - Depósitos";
//    $arClassificacao[3]["cod_classificacao"] = "4";
//    $arClassificacao[3]["nom_classificacao"] = "04 - Convênios";
//    $arClassificacao[4]["cod_classificacao"] = "5";
//    $arClassificacao[4]["nom_classificacao"] = "05 - Débitos de Tesouraria";
//    $arClassificacao[5]["cod_classificacao"] = "6";
//    $arClassificacao[5]["nom_classificacao"] = "06 - Sentenças Judiciais";
//    $arClassificacao[6]["cod_classificacao"] = "7";
//    $arClassificacao[6]["nom_classificacao"] = "07 - Outras Operações";
    $rsClassificacao = new Recordset();
    $rsClassificacao->preenche($this->getArrayClassificacao());
}

function consultaClassificacao($inCod_Classificacao)
{
    foreach ($this->getArrayClassificacao() as $arClassificacao) {
        if ($arClassificacao["cod_classificacao"] == $inCod_Classificacao) {
            return $arClassificacao["nom_classificacao"];
        }
    }
}

}
?>
