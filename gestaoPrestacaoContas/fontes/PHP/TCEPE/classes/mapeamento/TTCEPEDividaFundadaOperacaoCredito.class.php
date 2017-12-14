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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEPEDividaFundadaOperacaoCredito.class.php 60886 2014-11-20 18:27:12Z jean $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
 
class TTCEPEDividaFundadaOperacaoCredito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEPEDividaFundadaOperacaoCredito()
    {
        parent::Persistente();
        $this->setTabela('tcepe.divida_fundada_operacao_credito');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,tipo_operacao_credito,cod_norma,num_contrato');
        
        $this->AddCampo('exercicio'                        ,'varchar',true,'4', true,false);
        $this->AddCampo('cod_entidade'                     ,'integer',true, '', true,false);
        $this->AddCampo('tipo_operacao_credito'            ,'integer',true,'1', true,false);
        $this->AddCampo('cod_norma'                        ,'integer',true, '', true, true);
        $this->AddCampo('dt_assinatura'                    ,'date'   ,true, '',false,false);
        $this->AddCampo('num_contrato'                     ,'integer',true, '',false,false);
        $this->AddCampo('vl_saldo_anterior_titulo'         ,'numeric',true, '',false,false);
        $this->AddCampo('vl_inscricao_exercicio_titulo'    ,'numeric',true, '',false,false);
        $this->AddCampo('vl_baixa_exercicio_titulo'        ,'numeric',true, '',false,false);
        $this->AddCampo('vl_saldo_anterior_contrato'       ,'numeric',true, '',false,false);
        $this->AddCampo('vl_inscricao_exercicio_contrato'  ,'numeric',true, '',false,false);
        $this->AddCampo('vl_baixa_exercicio_contrato'      ,'numeric',true, '',false,false);
    }

    public function recuperaArquivoTCEPE(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaArquivoTCEPE().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }

    public function montaRecuperaArquivoTCEPE()
    { 
        $stSql = "  SELECT
                            0 AS reservado_tce
                            ,divida_fundada_operacao_credito.exercicio
                            ,divida_fundada_operacao_credito.tipo_operacao_credito AS tipo_credito
                            ,(LPAD(norma.num_norma::varchar ,5,'0') || norma.exercicio) AS num_lei
                            ,TO_CHAR(divida_fundada_operacao_credito.dt_assinatura,'ddmmyyyy') AS dt_lei
                            ,LPAD(divida_fundada_operacao_credito.num_contrato::varchar,5,'0') || divida_fundada_operacao_credito.exercicio  as num_contrato
                            ,divida_fundada_operacao_credito.vl_saldo_anterior_titulo AS saldo_anterior_titulos
                            ,divida_fundada_operacao_credito.vl_inscricao_exercicio_titulo AS credito_titulos
                            ,divida_fundada_operacao_credito.vl_baixa_exercicio_titulo AS baixa_credito_titulos
                            ,divida_fundada_operacao_credito.vl_saldo_anterior_contrato AS salvo_anterior_contrato
                            ,divida_fundada_operacao_credito.vl_inscricao_exercicio_contrato AS credito_contrato
                            ,divida_fundada_operacao_credito.vl_baixa_exercicio_contrato AS baixa_credito_contrato
                            
                    FROM tcepe.divida_fundada_operacao_credito
                    
                    JOIN normas.norma
                      ON norma.cod_norma = divida_fundada_operacao_credito.cod_norma
                    
                    WHERE divida_fundada_operacao_credito.cod_entidade IN (".$this->getDado('entidades').")
                    AND divida_fundada_operacao_credito.exercicio = '".$this->getDado('exercicio')."'                     
                ";
                
        return $stSql;
    }
    
    public function excluirDivida($boTransacao="") {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaExcluirDivida();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    /**
     * @author    Carlos Adriano <carlos.silva@cnm.org.br>
     * @return string
     */
    function montaExcluirDivida()
    {
       $stSql = "DELETE FROM tcepe.divida_fundada_operacao_credito
                       WHERE cod_entidade =  ".$this->getDado("cod_entidade")."
                         AND exercicio    = '".$this->getDado("exercicio")."'";
       
       return $stSql;
    }
}

?>