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
    * Classe de mapeamento da tabela CONTABILIDADE.LOTE
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-07-03 17:17:00 -0300 (Ter, 03 Jul 2007) $

    * Casos de uso: uc-02.02.04
                    uc-02.04.33
*/

/*
$Log$
Revision 1.11  2007/07/03 20:17:00  hboaventura
uc-02.04.33

Revision 1.10  2007/06/14 13:52:20  domluc
Ajustes dos Casos de Uso

Revision 1.9  2007/06/13 21:32:39  domluc
Recupera Ultimo Lote por Entidade/Exercicio

Revision 1.8  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeLote extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TContabilidadeLote()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.lote');
    
        $this->setCampoCod('cod_lote');
        $this->setComplementoChave('exercicio,tipo,cod_entidade');
    
        $this->AddCampo('cod_lote','integer',true,'',true,false);
        $this->AddCampo('exercicio','char',true,'4',true,false);
        $this->AddCampo('tipo','char',true,'1',true,false);
        $this->AddCampo('cod_entidade','integer',true,'',true,true );
        $this->AddCampo('nom_lote','varchar',true,'80',false,false);
        $this->AddCampo('dt_lote','date',true,'',false,false);
    }
    
    function recuperaUltimoLotePorEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
                    $stOrder = " order by cod_lote desc limit 1";
    
            return $this->executaRecupera("montaRecuperaUltimoLotePorEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    function montaRecuperaUltimoLotePorEntidade()
    {
        $stSql = " select lote.*
                                from contabilidade.lote
                               where lote.cod_entidade = " . $this->getDado("cod_entidade") . "
                                     and lote.exercicio = " . $this->getDado("exercicio"). "
                                     and lote.tipo = '" . $this->getDado("tipo") . "'
                            ";
    
        return $stSql;
    }
    
    public function excluirLote($boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaExcluirLote();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    /**
     * Método que retorna a conta crédito tributário da receita
     *
     * @author    Carlos Adriano <carlos.silva@cnm.org.br>
     * @return string
     */
    
    function montaExcluirLote()
    {
       $stSql = "DELETE FROM contabilidade.lote
                       WHERE nom_lote     = '".$this->getDado("nom_lote")."'
                         AND cod_entidade = ".$this->getDado("cod_entidade")."
                         AND exercicio    = '".$this->getDado("exercicio")."'
                         AND tipo         = '".$this->getDado("tipo")."'
                         AND dt_lote      = '".$this->getDado("dt_lote")."'";
       
       return $stSql;
    }

}
