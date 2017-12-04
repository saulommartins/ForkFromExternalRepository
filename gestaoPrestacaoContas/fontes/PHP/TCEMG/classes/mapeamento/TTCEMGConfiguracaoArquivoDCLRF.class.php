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
    * $Id: TTCEMGConfiguracaoArquivoDCLRF.class.php 64864 2016-04-08 17:03:33Z evandro $
    *
    * $Date: 2016-04-08 14:03:33 -0300 (Fri, 08 Apr 2016) $
    * $Author: evandro $
    * $Rev: 64864 $
*/

include_once( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );

class TTCEMGConfiguracaoArquivoDCLRF extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGConfiguracaoArquivoDCLRF()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configuracao_arquivo_dclrf');
        
        $this->setCampoCod('exercicio');
        $this->setComplementoChave('exercicio,mes_referencia');
        
        $this->AddCampo('exercicio'                                           , 'varchar',  true,    '4',  true, false);
        $this->AddCampo('mes_referencia'                                      , 'integer',  true,     '',  true, false);
        $this->AddCampo('valor_saldo_atual_concessoes_garantia'               , 'numeric', false, '14,2', false, false);
        $this->AddCampo('receita_privatizacao'                                , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_liquidado_incentivo_contribuinte'              , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_liquidado_incentivo_instituicao_financeira'    , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_inscrito_rpnp_incentivo_contribuinte'          , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_inscrito_rpnp_incentivo_instituicao_financeira', 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_compromissado'                                 , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_recursos_nao_aplicados'                        , 'numeric', false, '14,2', false, false);
        $this->AddCampo('publicacao_relatorio_lrf'                            , 'integer', false,     '', false, false);
        $this->AddCampo('dt_publicacao_relatorio_lrf'                         , 'date'   , false,     '', false, false);
        $this->AddCampo('bimestre'                                            , 'integer', false,     '', false, false);
        $this->AddCampo('meta_bimestral'                                      , 'integer', false,     '', false, false);
        $this->AddCampo('medida_adotada'                                      , 'text'   , false,     '', false, false);
        $this->AddCampo('cont_op_credito'                                     , 'integer', false,     '', false, false);
        $this->AddCampo('desc_cont_op_credito'                                , 'text'   , false,     '', false, false);
        $this->AddCampo('realiz_op_credito'                                   , 'integer', false,     '', false, false);
        $this->AddCampo('tipo_realiz_op_credito_capta'                        , 'integer', false,     '', false, false);
        $this->AddCampo('tipo_realiz_op_credito_receb'                        , 'integer', false,     '', false, false);
        $this->AddCampo('tipo_realiz_op_credito_assun_dir'                    , 'integer', false,     '', false, false);
        $this->AddCampo('tipo_realiz_op_credito_assun_obg'                    , 'integer', false,     '', false, false);
        $this->AddCampo('valor_saldo_atual_concessoes_garantia_interna'       , 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_saldo_atual_contra_concessoes_garantia_interna', 'numeric', false, '14,2', false, false);
        $this->AddCampo('valor_saldo_atual_contra_concessoes_garantia_externa', 'numeric', false, '14,2', false, false);
        $this->AddCampo('medidas_corretivas'                                  , 'text'   , false,     '', false, false);
    }    
    
    function recuperaValoresArquivoDCLRF(&$rsRecordSet) {
        return $this->executaRecupera("montaRecuperaValoresArquivoDCLRF",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    function montaRecuperaValoresArquivoDCLRF() {
        $stSql .= "
        SELECT exercicio ";
        
        if($this->getDado("cod_orgao") != ''){
            $stSql .= "
             , ".$this->getDado("tipo_registro")." AS tipo_registro
             , '".$this->getDado("cod_orgao")."'   AS cod_orgao
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
            , publicacao_relatorio_lrf
            , TO_CHAR(dt_publicacao_relatorio_lrf, 'DD/MM/YYYY') AS dt_publicacao_relatorio_lrf
            , bimestre
            , meta_bimestral
            , medida_adotada
            , cont_op_credito
            , desc_cont_op_credito
            , realiz_op_credito
            , tipo_realiz_op_credito_capta
            , tipo_realiz_op_credito_receb
            , tipo_realiz_op_credito_assun_dir
            , tipo_realiz_op_credito_assun_obg
            , valor_saldo_atual_concessoes_garantia_interna
            , valor_saldo_atual_contra_concessoes_garantia_interna
            , valor_saldo_atual_contra_concessoes_garantia_externa
            , medidas_corretivas
             
          FROM tcemg.configuracao_arquivo_dclrf 
         WHERE exercicio = '".$this->getDado('exercicio')."'
           AND mes_referencia = ".$this->getDado('mes_referencia');
           
        return $stSql;
    }
    
    public function __destruct(){}
}
