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

class TTCEMGRelatorioAnexo4 extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGRelatorioAnexo4()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    public function recuperaReceita(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReceita",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaReceita()
    {
        $stSql = "
                    SELECT *
                    
                      FROM tcemg.relatorio_anexo4_receita('".$this->getDado('stDataInicial')."','".$this->getDado('stDataFinal')."','".$this->getDado('exercicio')."')
                      AS retorno (
                                  cod_estrutural TEXT, 
                                  descricao TEXT, 
                                  nivel INTEGER, 
                                  valor NUMERIC,
                                  total NUMERIC,
                                  receita_ate_per NUMERIC
                                )
                                
                     ORDER BY nivel
        ";
        return $stSql;
    }
    
    public function recuperaReceitaLiquida(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReceitaLiquida",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaReceitaLiquida()
    {
        $stSql = "
                SELECT COALESCE(SUM(receita_corrente_liquida.valor),0.00) AS valor
                     
              FROM stn.receita_corrente_liquida
              
             WHERE receita_corrente_liquida.exercicio = '".$this->getDado('exercicio')."'
               AND receita_corrente_liquida.mes IN ( ".$this->getDado('meses_exercicio_anterior')." )
               AND receita_corrente_liquida.ano = '".$this->getDado('exercicio_anterior')."'
               AND receita_corrente_liquida.cod_entidade IN (1,2,3)
        ";
        return $stSql;
    }
    
    public function recuperaDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaDespesa()
    {
        $stSql = "
                    SELECT cod_estrutural,
                           tipo,
                           descricao,
                           COALESCE(empenhado,0.00) AS empenhado,
                           COALESCE(liquidado,0.00) AS liquidado,
                           COALESCE(pago,0.00)      AS pago,
                           COALESCE(empenhado_ate_periodo,0.00)      AS empenhado_ate_periodo
                      FROM tcemg.relatorio_anexo4_despesa('".$this->getDado('exercicio')."','".$this->getDado('stDataInicial')."',
                                                          '".$this->getDado('stDataFinal')."','','".$this->getDado("stTipoRelatorio")."','".$this->getDado('stRestos')."')
                      AS retorno (
                                  cod_estrutural    TEXT,
                                  nivel	            INTEGER,
                                  tipo	            INTEGER,
                                  descricao         TEXT,
                                  empenhado         NUMERIC,
                                  liquidado         NUMERIC,
                                  pago	            NUMERIC,
                                  empenhado_ate_periodo NUMERIC
                                )
                                
                     ORDER BY tipo, nivel
        ";
        
        return $stSql;
    }
    
    public function recuperaDespesaComPessoal(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesaComPessoal",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaDespesaComPessoal()
    {
        $stSql = "
                SELECT COALESCE(SUM(despesa_pessoal.valor),0.00) AS valor
                     
              FROM stn.despesa_pessoal
              
              WHERE despesa_pessoal.exercicio = '".$this->getDado('exercicio')."'
               AND despesa_pessoal.mes IN ( ".$this->getDado('meses_exercicio_anterior')." )
               AND despesa_pessoal.ano = '".$this->getDado('exercicio_anterior')."'
               AND despesa_pessoal.cod_entidade IN ( SELECT s_dp.cod_entidade FROM stn.despesa_pessoal AS s_dp WHERE s_dp.exercicio = '".$this->getDado('exercicio')."' GROUP BY s_dp.cod_entidade)
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>
