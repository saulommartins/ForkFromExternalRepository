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
  * Mapeamento folhapagamento.tcmba_gratificacao_funcao
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

class TTCMBAGratificacaoFuncao extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.tcmba_gratificacao_funcao');
        $this->setComplementoChave('exercicio', 'cod_entidade', 'cod_evento');
        
        $this->AddCampo('exercicio'    , 'varchar',  true, '4', true, true);
        $this->AddCampo('cod_entidade' , 'integer',  true,  '', true, true);
        $this->AddCampo('cod_evento'   , 'integer',  true,  '', true, true);
    }
    
    public function recuperaEventosGratificacaoFuncao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEventosGratificacaoFuncao().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEventosGratificacaoFuncao()
    {
        
        $stSql  ="  SELECT evento.cod_evento
                         , evento.codigo
                         , evento.descricao
                    
                      FROM folhapagamento.tcmba_gratificacao_funcao

                INNER JOIN folhapagamento.evento
                        ON evento.cod_evento = tcmba_gratificacao_funcao.cod_evento
              
                     WHERE tcmba_gratificacao_funcao.cod_entidade = ".$this->getDado('cod_entidade')."
                       AND tcmba_gratificacao_funcao.exercicio    = '".$this->getDado('exercicio')."' ";

        return $stSql;
    }
   
    public function __destruct(){}

}

?>