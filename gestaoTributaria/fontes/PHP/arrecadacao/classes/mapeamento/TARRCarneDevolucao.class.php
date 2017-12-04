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
  * Classe de mapeamento da tabela ARRECADACAO.CARNE_DEVOLUCAO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCarneDevolucao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.8  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.7  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.CARNE_DEVOLUCAO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRCarneDevolucao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRCarneDevolucao()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.carne_devolucao');

        $this->setCampoCod('numeracao');
        $this->setComplementoChave('timestamp,exercicio');

        $this->AddCampo('numeracao','varchar',true,'17',true,true);
        $this->AddCampo('cod_convenio','integer',true,'',true,true);
        $this->AddCampo('cod_motivo','integer',true,'',false,true);
        $this->AddCampo('dt_devolucao','date',true,'',false,false);
        $this->AddCampo('timestamp','timestamp',false,'',true,false);
    }

    public function recuperaListaCarnesParaCancelarNaParcela(&$rsRecordSet, $inCodParcela, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaCarnesParaCancelarNaParcela($inCodParcela);
        $this->setDebug( $stSql );
        #$this->debug(); //exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaCarnesParaCancelarNaParcela($inCodParcela)
    {
        $stSql = "
            SELECT
                numeracao,
                cod_convenio
            FROM
                arrecadacao.carne

            INNER JOIN
                arrecadacao.parcela
            ON
                parcela.cod_parcela = carne.cod_parcela

            WHERE
                parcela.cod_parcela = ".$inCodParcela."
                AND carne.numeracao NOT IN (
                    SELECT
                        pagamento.numeracao
                    FROM
                        arrecadacao.pagamento
                    WHERE
                        pagamento.numeracao = carne.numeracao
                        AND pagamento.cod_convenio = carne.cod_convenio
                )AND carne.numeracao NOT IN (
                    SELECT
                        carne_devolucao.numeracao
                    FROM
                        arrecadacao.carne_devolucao
                    WHERE
                        carne_devolucao.numeracao = carne.numeracao
                        AND carne_devolucao.cod_convenio = carne.cod_convenio
                )
        \n";

        return $stSql;
    }

}
?>
