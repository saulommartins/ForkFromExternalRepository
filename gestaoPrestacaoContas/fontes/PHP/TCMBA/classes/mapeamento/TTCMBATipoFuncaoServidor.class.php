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
  * Mapeamento tcmba.tipo_funcao_servidor
  * Data de Criação: 21/10/2015
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

class TTCMBATipoFuncaoServidor extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.tipo_funcao_servidor');
        $this->setComplementoChave('cod_tipo_funcao');

        $this->AddCampo('cod_tipo_funcao' , 'integer',  true,   '',  true,  true);
        $this->AddCampo('descricao'       , 'varchar',  true, '100', false, false);
    }
    
    public function recuperaFuncaoCargo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFuncaoCargo().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFuncaoCargo()
    {
        $stSql  =" SELECT tipo_funcao_servidor.cod_tipo_funcao
                        , tipo_funcao_servidor.descricao
             
                     FROM tcmba.tipo_funcao_servidor
             
               INNER JOIN folhapagamento.tcmba_cargo_servidor
                       ON tcmba_cargo_servidor.cod_tipo_funcao = tipo_funcao_servidor.cod_tipo_funcao
             
                    WHERE tcmba_cargo_servidor.cod_entidade = ".$this->getDado('cod_entidade')."
                      AND tcmba_cargo_servidor.exercicio    = '".$this->getDado('exercicio')."'
             
                 GROUP BY tipo_funcao_servidor.cod_tipo_funcao
                        , tipo_funcao_servidor.descricao
                 ORDER BY tipo_funcao_servidor.cod_tipo_funcao ";

        return $stSql;
    }
    
    public function __destruct(){}

}

?>