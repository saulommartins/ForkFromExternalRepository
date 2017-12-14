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

  * @author Analista:      
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGBALANCETE.class.php 62994 2015-07-15 12:39:47Z franver $
  $Date: 2015-07-15 09:39:47 -0300 (Qua, 15 Jul 2015) $
  $Author: franver $
  $Rev: 62994 $
*/

class TTCEMGBALANCETE extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGConfiguracaoOrgao()
    {
        parent::Persistente();
    }    

    public function recuperaRegistro10(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro10().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro10()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , ABS(saldo_inicial) AS saldo_inicial
                 , natureza_saldo_inicial
                 , ABS(total_debitos) AS total_debitos
                 , ABS(total_creditos) AS total_creditos
                 , ABS(saldo_final) AS saldo_final
                 , natureza_saldo_final
              FROM tcemg.fn_balancete_contabil_10('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro11(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro11().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro11()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , cod_orgao
                 , LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0')::VARCHAR AS cod_unidade_sub
                 , cod_funcao
                 , cod_sub_funcao
                 , cod_programa
                 , id_acao
                 , id_sub_acao
                 , natureza_despesa
                 , sub_elemento
                 , cod_fonte_recursos
                 , ABS(saldo_inicial_cd) AS vl_saldo_inicial
                 , natureza_saldo_inicial_cd
                 , ABS(total_debitos_cd) AS total_debitos_cd
                 , ABS(total_creditos_cd) AS total_creditos_cd
                 , ABS(saldo_final_cd) AS saldo_final_cd
                 , natureza_saldo_final_cd
              FROM tcemg.fn_balancete_contabil_11('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro12(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro12().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro12()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , natureza_receita
                 , cod_fonte_recursos
                 , ABS(saldo_inicial_cr) AS saldo_inicial_cr
                 , natureza_saldo_inicial_cr
                 , ABS(total_debitos_cr) AS total_debitos_cr
                 , ABS(total_creditos_cr) AS total_creditos_cr
                 , ABS(saldo_final_cr) AS saldo_final_cr
                 , natureza_saldo_final_cr
              FROM tcemg.fn_balancete_contabil_12('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro13(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro13().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro13()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , cod_programa
                 , id_acao
                 , id_sub_acao
                 , ABS(saldo_inicial_pa) AS saldo_inicial_pa
                 , natureza_saldo_inicial_pa
                 , ABS(total_debitos_pa) AS total_debitos_pa
                 , ABS(total_creditos_pa) AS total_creditos_pa
                 , ABS(saldo_final_pa) AS saldo_final_pa
                 , natureza_saldo_final_pa
              FROM tcemg.fn_balancete_contabil_13('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro14(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro14().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro14()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , cod_orgao
                 , num_orgao
                 , num_unidade
                 , cod_funcao
                 , cod_sub_funcao
                 , cod_programa
                 , id_acao
                 , id_sub_acao
                 , natureza_despesa
                 , sub_elemento
                 , cod_fonte_recursos
                 , numero_empenho
                 , ano_inscricao
                 , ABS(saldo_inicial_rsp) AS saldo_inicial_rsp
                 , natureza_saldo_inicial_rsp
                 , ABS(total_debitos_rsp) AS total_debitos_rsp
                 , ABS(total_creditos_rsp) AS total_creditos_rsp
                 , ABS(saldo_final_rsp) AS saldo_final_rsp
                 , natureza_saldo_final_rsp
              FROM tcemg.fn_balancete_contabil_14('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro15(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro15().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro15()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , atributo_sf
                 , ABS(saldo_inicial_sf) AS saldo_inicial_sf
                 , natureza_saldo_inicial_sf
                 , ABS(total_debitos_sf) AS total_debitos_sf
                 , ABS(total_creditos_sf) AS total_creditos_sf
                 , ABS(saldo_final_sf) AS saldo_final_sf
                 , natureza_saldo_final_sf
              FROM tcemg.fn_balancete_contabil_15('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro16(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro16().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro16()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , cod_fonte_recursos
                 , ABS(saldo_inicial_fonte_sf) AS saldo_inicial_fonte_sf
                 , natureza_saldo_inicial_fonte_sf
                 , ABS(total_debitos_fonte_sf) AS total_debitos_fonte_sf
                 , ABS(total_creditos_fonte_sf) AS total_creditos_fonte_sf
                 , ABS(saldo_final_fonte_sf) AS saldo_final_fonte_sf
                 , natureza_saldo_final_fonte_sf
              FROM tcemg.fn_balancete_contabil_16('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro17(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro17().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro17()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , atributo_sf
                 , cod_ctb
                 , cod_fonte_recursos
                 , ABS(saldo_inicial_ctb) AS saldo_inicial_ctb
                 , natureza_saldo_inicial_ctb
                 , ABS(total_debitos_ctb) AS total_debitos_ctb
                 , ABS(total_creditos_ctb) AS total_creditos_ctb
                 , ABS(saldo_final_ctb) AS saldo_final_ctb
                 , natureza_saldo_final_ctb
              FROM tcemg.fn_balancete_contabil_17('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro18(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro18().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro18()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , cod_fonte_recursos
                 , ABS(saldo_inicial_fr) AS saldo_inicial_fr
                 , natureza_saldo_inicial_fr
                 , ABS(total_debitos_fr) AS total_debitos_fr
                 , ABS(total_creditos_fr) AS total_creditos_fr
                 , ABS(saldo_final_fr) AS saldo_final_fr
                 , natureza_saldo_final_fr
              FROM tcemg.fn_balancete_contabil_18('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

    public function recuperaRegistro22(&$rsRecordSet)
    {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
    
       if(trim($stOrdem))
           $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
       $stSql = $this->montaRecuperaRegistro22().$stCondicao.$stOrdem;
       $this->setDebug( $stSql );
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
       return $obErro;
    }
    
    public function montaRecuperaRegistro22()
    {
	    $stSql = "
            SELECT tipo_registro
                 , SUBSTR(REPLACE(conta_contabil, '.',''),1,9) AS conta_contabil
                 , atributo_sf
                 , cod_ctb
                 , ABS(saldo_inicial_ctb_sf) AS saldo_inicial_ctb_sf
                 , natureza_saldo_inicial_ctb_sf
                 , ABS(total_debitos_ctb_sf) AS total_debitos_ctb_sf
                 , ABS(total_creditos_ctb_sf) AS total_creditos_ctb_sf
                 , ABS(saldo_final_ctb_sf) AS saldo_final_ctb_sf
                 , natureza_saldo_final_ctb_sf
              FROM tcemg.fn_balancete_contabil_22('".$this->getDado('exercicio')."',' cod_entidade IN ( ".$this->getDado('cod_entidade')." ) ','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."')
          ORDER BY conta_contabil
	    ";
	    return $stSql;
    }

}
?>