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
  * Mapeamento tcmba.tcmba_cargo_servidor_temporario
  * Data de Criação: 28/10/2015
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

class TTCMBACargoServidorTemporario extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.tcmba_cargo_servidor_temporario');
        $this->setComplementoChave('exercicio, cod_entidade, cod_tipo_funcao, cod_cargo');

        $this->AddCampo('exercicio'       , 'varchar',  true, '4', true, true);
        $this->AddCampo('cod_entidade'    , 'integer',  true,  '', true, true);
        $this->AddCampo('cod_tipo_funcao' , 'integer',  true,  '', true, true);
        $this->AddCampo('cod_cargo'       , 'integer',  true,  '', true, true);
    }
    
    public function recuperaCargosTemporario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaCargosTemporario().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCargosTemporario()
    {
        $stSql  ="  SELECT tipo_funcao_servidor_temporario.cod_tipo_funcao
                         , INITCAP(cargo.descricao) AS descricao
                         , tcmba_cargo_servidor_temporario.cod_cargo
                    
                      FROM folhapagamento.tcmba_cargo_servidor_temporario
              
                INNER JOIN tcmba.tipo_funcao_servidor_temporario
                        ON tcmba_cargo_servidor_temporario.cod_tipo_funcao = tipo_funcao_servidor_temporario.cod_tipo_funcao
                        
                INNER JOIN pessoal.cargo
                        ON cargo.cod_cargo = tcmba_cargo_servidor_temporario.cod_cargo
              
                     WHERE tcmba_cargo_servidor_temporario.cod_entidade    = ".$this->getDado('cod_entidade')."
                       AND tcmba_cargo_servidor_temporario.exercicio       = '".$this->getDado('exercicio')."'
                       AND tipo_funcao_servidor_temporario.cod_tipo_funcao = ".$this->getDado('cod_tipo_funcao');

        return $stSql;
    }

    public function __destruct(){}

}

?>