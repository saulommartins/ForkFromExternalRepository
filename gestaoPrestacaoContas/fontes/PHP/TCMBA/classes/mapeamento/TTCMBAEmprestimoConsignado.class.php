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
  * Mapeamento tcmba.tcmba_emprestimo_consignado
  * Data de Criação: 29/10/2015
  * 
  * @author Analista:      Dagiane Vieira
  * @author Desenvolvedor: Arthur Cruz
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
require_once CLA_PERSISTENTE;

class TTCMBAEmprestimoConsignado extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.tcmba_emprestimo_consignado');
        $this->setComplementoChave('exercicio', 'cod_entidade', 'cod_banco', 'cod_evento');
        
        $this->AddCampo('exercicio'    , 'varchar',  true, '4', true, true);
        $this->AddCampo('cod_entidade' , 'integer',  true,  '', true, true);
        $this->AddCampo('cod_banco'    , 'integer',  true,  '', true, true);
        $this->AddCampo('cod_evento'   , 'integer',  true,  '', true, true);
    }
    
    public function recuperaBancosEmprestimo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaBancosEmprestimo().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaBancosEmprestimo()
    {
        $stSql  ="  SELECT banco.cod_banco
                         , banco.num_banco
                         , banco.nom_banco
             
                     FROM monetario.banco
             
               INNER JOIN folhapagamento.tcmba_emprestimo_consignado
                       ON tcmba_emprestimo_consignado.cod_banco = banco.cod_banco
             
                    WHERE folhapagamento.tcmba_emprestimo_consignado.cod_entidade = ".$this->getDado('cod_entidade')."
                      AND folhapagamento.tcmba_emprestimo_consignado.exercicio    = '".$this->getDado('exercicio')."'
             
                 GROUP BY banco.num_banco
                        , banco.nom_banco
                        , banco.cod_banco
                 ORDER BY banco.num_banco ";

        return $stSql;
    }
    
     public function recuperaEventosEmprestimo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEventosEmprestimo().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEventosEmprestimo()
    {
        $stSql  ="  SELECT banco.num_banco
                         , banco.nom_banco
                         , evento.cod_evento
                         , evento.descricao
                         , evento.codigo
                    
                      FROM folhapagamento.tcmba_emprestimo_consignado
              
                INNER JOIN monetario.banco
                        ON banco.cod_banco = tcmba_emprestimo_consignado.cod_banco

                INNER JOIN folhapagamento.evento
                        ON tcmba_emprestimo_consignado.cod_evento = evento.cod_evento
              
                     WHERE tcmba_emprestimo_consignado.cod_entidade = ".$this->getDado('cod_entidade')."
                       AND tcmba_emprestimo_consignado.exercicio    = '".$this->getDado('exercicio')."'
                       AND banco.cod_banco = ".$this->getDado('cod_banco');

        return $stSql;
    }

    public function __destruct(){}

}

?>