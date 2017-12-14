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
    * Classe de mapeamento da tabela DIVIDA.PARCELAMENTO
    * Data de Criação: 29/01/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATParcelamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.4  2007/03/26 15:32:36  cercato
alterando mapeamento do parcelamento em funcao do campo novo desta tabela.

Revision 1.3  2007/03/12 19:21:25  cercato
correcao na funcao que retorna numero do parcelamento.

Revision 1.2  2007/02/23 18:49:16  cercato
alteracoes em funcao das mudancas no ER.

Revision 1.1  2007/02/09 18:29:13  cercato
correcoes para divida.cobranca

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATParcelamento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATParcelamento()
    {
        parent::Persistente();
        $this->setTabela('divida.parcelamento');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento');

        $this->AddCampo('num_parcelamento','integer',true,'',true,false);
        $this->AddCampo('numcgm_usuario','integer',false,'',false,true);
        $this->AddCampo('timestamp_modalidade','timestamp',false,'',false,true);
        $this->AddCampo('cod_modalidade','integer',false,'',false,true);
        $this->AddCampo('numero_parcelamento','integer',false,'',false,false);
        $this->AddCampo('exercicio','varchar',false,'4',false,false);
        $this->AddCampo('timestamp','timestamp',false,'',false,false);
        $this->AddCampo('judicial','boolean',false,'',false,false);
    }

    public function recuperaNumeroParcelamento(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNumeroParcelamento();
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaNumeroParcelamento()
    {
        $stSql  = " SELECT                                                      \n";
        $stSql .= "     coalesce ( (max(ddp.num_parcelamento) +1), 0 ) as valor \n";
        $stSql .= " FROM                                                        \n";
        $stSql .= "     divida.parcelamento as ddp                              \n";

        return $stSql;
    }

    public function recuperaNumeroParcelamentoExercicio(&$rsRecordSet, $stExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNumeroParcelamentoExercicio($stExercicio);
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaNumeroParcelamentoExercicio($stExercicio)
    {
        $stSql  = " SELECT                                                           \n";
        $stSql .= "     coalesce ( (max(ddp.numero_parcelamento) +1), 1 ) as valor   \n";
        $stSql .= " FROM                                                             \n";
        $stSql .= "     divida.parcelamento as ddp                                   \n";
        $stSql .= " WHERE                                                            \n";
        $stSql .= "     ddp.exercicio = '".$stExercicio."'                            \n";

        return $stSql;
    }

    public function recuperaNumeroParcelamentoPorInscricao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNumeroParcelamentoPorInscricao().$stFiltro;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNumeroParcelamentoPorInscricao()
    {
        $stSql  = " SELECT
                        ddp.num_parcelamento

                    FROM
                        divida.divida_parcelamento AS ddp

                    INNER JOIN
                        divida.parcelamento AS dp
                    ON
                        ddp.num_parcelamento = dp.num_parcelamento \n";

        return $stSql;

    }

}// end of class
?>
