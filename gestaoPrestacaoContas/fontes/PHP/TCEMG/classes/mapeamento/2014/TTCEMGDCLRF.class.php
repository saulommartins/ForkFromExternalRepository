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
    * Classe de mapeamento da tabela TCEMG.METAS_FISCAIS
    * Data de Criação: 20/02/2014
    
    
    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes
    
    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCEMGDCLRF.class.php 62297 2015-04-20 17:12:11Z franver $
    *
    * $Date: 2015-04-20 14:12:11 -0300 (Mon, 20 Apr 2015) $
    * $Author: franver $
    * $Rev: 62297 $
*/

include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );

class TTCEMGDCLRF extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGDCLRF()
    {
        parent::Persistente();
    }
    
    function recuperaValoresArquivoDCLRF(&$rsRecordSet)
    {
        return $this->executaRecupera("montaRecuperaValoresArquivoDCLRF",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    function montaRecuperaValoresArquivoDCLRF()
    {
        $stSql .= "
        SELECT exercicio ";
        
        if($this->getDado("cod_orgao") != ''){
            $stSql .= "
             , 10 AS tipo_registro
             , '".$this->getDado("cod_orgao")."' AS cod_orgao
            ";
        }
        
        $stSql .= "
             , mes_referencia
             , REPLACE(valor_saldo_atual_concessoes_garantia::VARCHAR, '.',',') AS valor_saldo_atual_concessoes_garantia
             , REPLACE(receita_privatizacao::VARCHAR, '.',',') AS receita_privatizacao
             , REPLACE(valor_liquidado_incentivo_contribuinte::VARCHAR, '.',',') AS valor_liquidado_incentivo_contribuinte
             , REPLACE(valor_liquidado_incentivo_instituicao_financeira::VARCHAR, '.',',') AS valor_liquidado_incentivo_instituicao_financeira
             , REPLACE(valor_inscrito_rpnp_incentivo_contribuinte::VARCHAR, '.',',') AS valor_inscrito_rpnp_incentivo_contribuinte
             , REPLACE(valor_inscrito_rpnp_incentivo_instituicao_financeira::VARCHAR, '.',',') AS valor_inscrito_rpnp_incentivo_instituicao_financeira
             , REPLACE(valor_compromissado::VARCHAR, '.',',') AS valor_compromissado
             , REPLACE(valor_recursos_nao_aplicados::VARCHAR, '.',',') AS valor_recursos_nao_aplicados
          FROM tcemg.configuracao_arquivo_dclrf 
         WHERE exercicio = '".$this->getDado('exercicio')."'
           AND mes_referencia = ".$this->getDado('mes_referencia');
        return $stSql;
    }
    
    public function __destruct(){}

}
?>