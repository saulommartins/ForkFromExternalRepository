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
    * Classe de regra de negócio Pessoa Física
    * Data de Criação: 16/01/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Regra

    * $Id: RExportacaoTCEMGArqUniOrcam.class.php 59612 2014-09-02 12:00:51Z gelson $
    * $Name: $
    * $Revision: 59612 $
    * $Author: gelson $
    * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCEMGUniOrcam.class.php"  	    );

class RExportacaoTCEMGArqUniOrcam
{
var $arUniOrcam;
var $roUltimaUniOrcam;
var $obRExportacaoTCEMGUniOrcam;
var $obTransacao;

//SETTERS
function setUniOrcam($valor) { $this->arUniOrcam = $valor;         }
function setUltimaUniOrcam($valor) { $this->roUltimaUniOrcam = $valor;   }
function setExportacaoTCEMGUniOrcam($valor) { $this->obRExportacaoTCEMGUniOrcam = $valor;   }

//GETTERS
function getUniOrcam() { return $this->arUniOrcam;           }
function getUltimaUniOrcam() { return $this->roUltimaUniOrcam;     }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RExportacaoTCEMGArqUniOrcam()
{
    $this->setUniOrcam( array() );
    $this->setExportacaoTCEMGUniOrcam( new RExportacaoTCEMGUniOrcam() );
    $this->obTransacao =  new Transacao();
}

function addUniOrcam()
{
    $this->arUniOrcam[] = new RExportacaoTCEMGUniOrcam();
    $this->roUltimaUniOrcam = &$this->arUniOrcam[ count( $this->arUniOrcam ) -1 ];
}

function salvar($boTransacao='')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arUniOrcam as $obRExportacaoTCEMGUniOrcam) {
            $obErro = $obRExportacaoTCEMGUniOrcam->salvar($boTransacao);
            if($obErro->ocorreu())
                break;
            }
        }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obRExportacaoTCEMGUniOrcam );

    return $obErro;
}

function listarIdentificador(&$rsIdentificador)
{
    $arIdentificador[0]["identificador"] = "1";
    $arIdentificador[0]["cod_identificador"] = "01";
    $arIdentificador[0]["nom_identificador"] = "FUNDEB";
    $arIdentificador[1]["identificador"] = "2";
    $arIdentificador[1]["cod_identificador"] = "02";
    $arIdentificador[1]["nom_identificador"] = "FMS - Fundo Municipal de Saúde";
    $arIdentificador[2]["identificador"] = "3";
    $arIdentificador[2]["cod_identificador"] = "03";
    $arIdentificador[2]["nom_identificador"] = "FMAS - Fundo Municipal de Assitência Social";
    $arIdentificador[3]["identificador"] = "4";
    $arIdentificador[3]["cod_identificador"] = "04";
    $arIdentificador[3]["nom_identificador"] = "FMCA - Fundo Municipal da Criança e do Adolescente";
    $arIdentificador[4]["identificador"] = "99";
    $arIdentificador[4]["cod_identificador"] = "99";
    $arIdentificador[4]["nom_identificador"] = "Outros Fundos";
    $rsIdentificador = new Recordset();
    $rsIdentificador->preenche($arIdentificador);
}

}
?>
