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
  * Mapeamento folhapagamento.tcmba_salario_horas_extras
  * Data de Criação: 03/11/2015
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

class TTCMBASalarioHorasExtras extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.tcmba_salario_horas_extras');
        $this->setComplementoChave('exercicio', 'cod_entidade', 'cod_evento');
        
        $this->AddCampo('exercicio'    , 'varchar',  true, '4', true, true);
        $this->AddCampo('cod_entidade' , 'integer',  true,  '', true, true);
        $this->AddCampo('cod_evento'   , 'integer',  true,  '', true, true);
    }
    
    public function recuperaEventosHorasExtras(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEventosHorasExtras().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEventosHorasExtras()
    {
        
        $stSql  ="  SELECT evento.cod_evento
                         , evento.codigo
                         , evento.descricao
                    
                      FROM folhapagamento.tcmba_salario_horas_extras

                INNER JOIN folhapagamento.evento
                        ON evento.cod_evento = tcmba_salario_horas_extras.cod_evento
              
                     WHERE tcmba_salario_horas_extras.cod_entidade = ".$this->getDado('cod_entidade')."
                       AND tcmba_salario_horas_extras.exercicio    = '".$this->getDado('exercicio')."' ";

        return $stSql;
    }
   
    public function __destruct(){}

}

?>