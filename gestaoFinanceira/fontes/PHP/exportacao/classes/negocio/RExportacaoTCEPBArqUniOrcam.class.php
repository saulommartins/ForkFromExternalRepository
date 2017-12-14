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
    * Data de Criação: 22/07/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Lisiane Morais
    * 
    * @package URBEM
    * @subpackage Regra

    * $Id: RExportacaoTCEPBArqUniOrcam.class.php 59612 2014-09-02 12:00:51Z gelson $
    * $Name: $
    * $Revision: $
    * $Author:$
    * $Date:$

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCEPBUniOrcam.class.php"  	    );

class RExportacaoTCEPBArqUniOrcam
{
var $arUniOrcam;
var $roUltimaUniOrcam;
var $obRExportacaoTCEPBUniOrcam;
var $obTransacao;

//SETTERS
function setUniOrcam($valor) { $this->arUniOrcam = $valor;         }
function setUltimaUniOrcam($valor) { $this->roUltimaUniOrcam = $valor;   }
function setExportacaoTCEPBUniOrcam($valor) { $this->obRExportacaoTCEPBUniOrcam = $valor;   }

//GETTERS
function getUniOrcam() { return $this->arUniOrcam;           }
function getUltimaUniOrcam() { return $this->roUltimaUniOrcam;     }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RExportacaoTCEPBArqUniOrcam()
{
    $this->setUniOrcam( array() );
    $this->setExportacaoTCEPBUniOrcam( new RExportacaoTCEPBUniOrcam() );
    $this->obTransacao =  new Transacao();
}

function addUniOrcam()
{
    $this->arUniOrcam[] = new RExportacaoTCEPBUniOrcam();
    $this->roUltimaUniOrcam = &$this->arUniOrcam[ count( $this->arUniOrcam ) -1 ];
}

function salvar($boTransacao='')
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arUniOrcam as $obRExportacaoTCEPBUniOrcam) {
            $obErro = $obRExportacaoTCEPBUniOrcam->salvar($boTransacao);
            if($obErro->ocorreu())
                break;
            }
        }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obRExportacaoTCEPBUniOrcam );

    return $obErro;
}

function listarNaturezaJuridica(&$rsNaturezaJuridica)
{
    $arNaturezaJuridica[0]["natureza_juridica"] = "1";
    $arNaturezaJuridica[0]["cod_natureza_juridica"] = "1";
    $arNaturezaJuridica[0]["nom_natureza_juridica"] = "Câmara Municipal";
    $arNaturezaJuridica[1]["natureza_juridica"] = "2";
    $arNaturezaJuridica[1]["cod_natureza_juridica"] = "2";
    $arNaturezaJuridica[1]["nom_natureza_juridica"] = "Prefeitura Municipal/Secretarias";
    $arNaturezaJuridica[2]["natureza_juridica"] = "3";
    $arNaturezaJuridica[2]["cod_natureza_juridica"] = "3";
    $arNaturezaJuridica[2]["nom_natureza_juridica"] = "Autarquia";
    $arNaturezaJuridica[3]["natureza_juridica"] = "4";
    $arNaturezaJuridica[3]["cod_natureza_juridica"] = "4";
    $arNaturezaJuridica[3]["nom_natureza_juridica"] = "Fundação";
    $arNaturezaJuridica[4]["natureza_juridica"] = "5";
    $arNaturezaJuridica[4]["cod_natureza_juridica"] = "5";
    $arNaturezaJuridica[4]["nom_natureza_juridica"] = "Sociedade de Economia Mista";
    $arNaturezaJuridica[5]["natureza_juridica"] = "6";
    $arNaturezaJuridica[5]["cod_natureza_juridica"] = "6";
    $arNaturezaJuridica[5]["nom_natureza_juridica"] = "Fundos";
    $arNaturezaJuridica[6]["natureza_juridica"] = "7";
    $arNaturezaJuridica[6]["cod_natureza_juridica"] = "7";
    $arNaturezaJuridica[6]["nom_natureza_juridica"] = "Empresas Públicas";
    $arNaturezaJuridica[7]["natureza_juridica"] = "8";
    $arNaturezaJuridica[7]["cod_natureza_juridica"] = "8";
    $arNaturezaJuridica[7]["nom_natureza_juridica"] = "Autarquia Previdenciária";
    $arNaturezaJuridica[8]["natureza_juridica"] = "9";
    $arNaturezaJuridica[8]["cod_natureza_juridica"] = "9";
    $arNaturezaJuridica[8]["nom_natureza_juridica"] = "Fundo Previdenciário";
    $rsNaturezaJuridica = new Recordset();
    $rsNaturezaJuridica->preenche($arNaturezaJuridica);
}
}
?>
