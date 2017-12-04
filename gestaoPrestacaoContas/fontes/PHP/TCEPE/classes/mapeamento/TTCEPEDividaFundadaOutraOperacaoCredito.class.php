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
  * @author Desenvolvedor: Carlos Adriano

  * @ignore

  $Id: TTCEPEDividaFundadaOutraOperacaoCredito.class.php 60739 2014-11-12 18:47:23Z franver $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEPEDividaFundadaOutraOperacaoCredito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEPEDividaFundadaOutraOperacaoCredito()
    {
        parent::Persistente();
        $this->setTabela('tcepe.divida_fundada_outra_operacao_credito');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,cod_norma,num_contrato');
        
        $this->AddCampo('exercicio'             , 'varchar', true,'4', true , false);
        $this->AddCampo('cod_entidade'          , 'integer', true, '', true , false);
        $this->AddCampo('cod_norma'             , 'integer', true, '', true ,  true);
        $this->AddCampo('dt_assinatura'         , 'date'   , true, '', false, false);
        $this->AddCampo('num_contrato'          , 'integer', true, '', false, false);
        $this->AddCampo('cgm_credor'            , 'integer', true, '', false, false);
        $this->AddCampo('vl_saldo_anterior'     , 'numeric', true, '', false, false);
        $this->AddCampo('vl_inscricao_exercicio', 'numeric', true, '', false, false);
        $this->AddCampo('vl_baixa_exercicio'    , 'numeric', true, '', false, false);
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
       $stSql = "DELETE FROM tcepe.divida_fundada_outra_operacao_credito
                       WHERE cod_entidade =  ".$this->getDado("cod_entidade")."
                         AND exercicio    = '".$this->getDado("exercicio")."'";
       
       return $stSql;
    }
    
    public function recuperaDividaFundadaOutraOperacaoCredito(&$rsRecordSet, $boTransacao)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDividaFundadaOutraOperacaoCredito();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaDividaFundadaOutraOperacaoCredito()
    {
        $stSql = "
            SELECT divida_fundada_outra_operacao_credito.exercicio AS exercicio
                 , LPAD(norma.num_norma::VARCHAR, 5, '0')||TO_CHAR(norma.dt_assinatura, 'YYYY') AS num_lei
                 , TO_CHAR(norma.dt_assinatura, 'DDMMYYYY') AS dt_lei
                 , LPAD(divida_fundada_outra_operacao_credito.num_contrato::VARCHAR, 5, '0')||divida_fundada_outra_operacao_credito.exercicio AS num_contrato
                 , sw_cgm.nom_cgm AS nome_credor_divida
                 , divida_fundada_outra_operacao_credito.vl_saldo_anterior AS saldo_anterior
                 , divida_fundada_outra_operacao_credito.vl_inscricao_exercicio AS inscrições_exercicio
                 , divida_fundada_outra_operacao_credito.vl_baixa_exercicio AS baixa_exercicio
              FROM tcepe.divida_fundada_outra_operacao_credito
        INNER JOIN normas.norma
                ON norma.cod_norma = divida_fundada_outra_operacao_credito.cod_norma
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = divida_fundada_outra_operacao_credito.cgm_credor
             WHERE divida_fundada_outra_operacao_credito.cod_entidade = ".$this->getDado("entidades")."
               AND divida_fundada_outra_operacao_credito.exercicio = '".$this->getDado("exercicio")."'
        ";
        return $stSql;
    }
}

?>