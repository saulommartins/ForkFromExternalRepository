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
    * Pacote de configuração do TCETO - Negócio Arquivo Unidade Orçamentária
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: RExportacaoTCETOArqUniOrcam.class.php 60654 2014-11-06 13:18:49Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCETO_NEGOCIO."RExportacaoTCETOUniOrcam.class.php";

class RExportacaoTCETOArqUniOrcam
{
    var $arUniOrcam;
    var $roUltimaUniOrcam;
    var $obRExportacaoTCETOUniOrcam;
    var $obTransacao;
    
    //SETTERS
    function setUniOrcam($valor) { $this->arUniOrcam                                = $valor;   }
    function setUltimaUniOrcam($valor) { $this->roUltimaUniOrcam                    = $valor;   }
    function setExportacaoTCETOUniOrcam($valor) { $this->obRExportacaoTCETOUniOrcam = $valor;   }
    
    //GETTERS
    function getUniOrcam() { return $this->arUniOrcam;              }
    function getUltimaUniOrcam() { return $this->roUltimaUniOrcam;  }
    
    //METODO CONSTRUTOR
    /**
         * Método construtor
         * @access Private
    */
    function RExportacaoTCETOArqUniOrcam()
    {
        $this->setUniOrcam( array() );
        $this->setExportacaoTCETOUniOrcam( new RExportacaoTCETOUniOrcam() );
        $this->obTransacao =  new Transacao();
    }
    
    function addUniOrcam()
    {
        $this->arUniOrcam[] = new RExportacaoTCETOUniOrcam();
        $this->roUltimaUniOrcam = &$this->arUniOrcam[ count( $this->arUniOrcam ) -1 ];
    }
    
    function salvar($boTransacao='')
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            foreach ($this->arUniOrcam as $obRExportacaoTCETOUniOrcam) {
                $obErro = $obRExportacaoTCETOUniOrcam->salvar($boTransacao);
                if($obErro->ocorreu())
                    break;
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    
        return $obErro;
    }
    
    function listarIdentificador(&$rsIdentificador)
    {
        $arIdentificador[0]["identificador"]        = "1";
        $arIdentificador[0]["cod_identificador"]    = "01";
        $arIdentificador[0]["nom_identificador"]    = "Prefeitura Municipal";
        
        $arIdentificador[1]["identificador"]        = "2";
        $arIdentificador[1]["cod_identificador"]    = "02";
        $arIdentificador[1]["nom_identificador"]    = "Câmara Municipal";
        
        $arIdentificador[2]["identificador"]        = "3";
        $arIdentificador[2]["cod_identificador"]    = "03";
        $arIdentificador[2]["nom_identificador"]    = "Secretaria da Educação";
        
        $arIdentificador[3]["identificador"]        = "4";
        $arIdentificador[3]["cod_identificador"]    = "04";
        $arIdentificador[3]["nom_identificador"]    = "Fundo Municipal";
        
        $arIdentificador[4]["identificador"]        = "5";
        $arIdentificador[4]["cod_identificador"]    = "05";
        $arIdentificador[4]["nom_identificador"]    = "RPPS (exceto Autarquia)";
        
        $arIdentificador[5]["identificador"]        = "6";
        $arIdentificador[5]["cod_identificador"]    = "06";
        $arIdentificador[5]["nom_identificador"]    = "Autarquia (exceto RPPS)";
        
        $arIdentificador[6]["identificador"]        = "7";
        $arIdentificador[6]["cod_identificador"]    = "07";
        $arIdentificador[6]["nom_identificador"]    = "Autarquia (RPPS)";
        
        $arIdentificador[7]["identificador"]        = "8";
        $arIdentificador[7]["cod_identificador"]    = "08";
        $arIdentificador[7]["nom_identificador"]    = "Fundação";
        
        $arIdentificador[8]["identificador"]        = "9";
        $arIdentificador[8]["cod_identificador"]    = "09";
        $arIdentificador[8]["nom_identificador"]    = "Empresa Estatal Dependente";
        
        $arIdentificador[9]["identificador"]        = "10";
        $arIdentificador[9]["cod_identificador"]    = "10";
        $arIdentificador[9]["nom_identificador"]    = "Empresa Estatal Não Dependente";
        
        $arIdentificador[10]["identificador"]       = "11";
        $arIdentificador[10]["cod_identificador"]   = "11";
        $arIdentificador[10]["nom_identificador"]   = "Consórcio";
        
        $arIdentificador[11]["identificador"]       = "12";
        $arIdentificador[11]["cod_identificador"]   = "12";
        $arIdentificador[11]["nom_identificador"]   = "Outros";
    
        $rsIdentificador = new Recordset();
        $rsIdentificador->preenche($arIdentificador);
    }

}
?>
