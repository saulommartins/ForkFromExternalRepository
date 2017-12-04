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
    * Classe de mapeamento da tabela TESOURARIA_ARRECADACAO_CARNE
    * Data de Criação: 16/12/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-16 17:36:26 -0300 (Qui, 16 Ago 2007) $

    * Casos de uso: uc-02.04.04
*/

/*
$Log$
Revision 1.4  2007/08/16 20:36:26  hboaventura
Bug#9911#

Revision 1.3  2007/07/25 15:50:34  domluc
*** empty log message ***

Revision 1.2  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_ARRECADACAO_CARNE
  * Data de Criação: 16/12/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaArrecadacaoCarne extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaArrecadacaoCarne()
{
    parent::Persistente();
    $this->setTabela("tesouraria.arrecadacao_carne");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_arrecadacao,timestamp_arrecadacao,numeracao,exercicio');

    $this->AddCampo('cod_arrecadacao'       , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('timestamp_arrecadacao' , 'timestamp', true, ''  , true  , false );
    $this->AddCampo('numeracao'             , 'varchar'  , true, '17', true , true  );
    $this->AddCampo('cod_convenio'          , 'integer'  , true, '', true , true  );
    $this->AddCampo('exercicio'             , 'char'     , true, '4' , true  , true  );
}

    public function recuperaFuncaoListaPagamentosNumeracao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaFuncaoListaPagamentosNumeracao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaFuncaoListaPagamentosNumeracao()
    {
        $stSql = "
                    select *
                      from tesouraria.fn_creditos_por_carne
                          (	 '" . $this->getDado("numeracao") ."'
                              ," . $this->getDado("cod_convenio") ."
                              ," . $this->getDado("exercicio") ."
                          )
                      as
                          (   numeracao varchar
                              , cod_convenio int
                              , codigo int
                                    , tipo varchar
                              , soma numeric(14,2)
                          )
        ";

        return $stSql;
    }

    public function recuperaCreditosNumeracao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCreditosNumeracao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCreditosNumeracao()
    {
        $stSql = "
            SELECT  calculo.cod_credito
                 ,  calculo.cod_especie
                 ,  calculo.cod_genero
                 ,  calculo.cod_natureza
              FROM  arrecadacao.calculo
        INNER JOIN  arrecadacao.lancamento_calculo
                ON  lancamento_calculo.cod_calculo = calculo.cod_calculo
        INNER JOIN  arrecadacao.parcela
                ON  parcela.cod_lancamento = lancamento_calculo.cod_lancamento
        INNER JOIN  arrecadacao.carne
                ON  carne.cod_parcela = parcela.cod_parcela
             WHERE  carne.numeracao = ".$this->getDado('numeracao')."
               AND  carne.exercicio = '".$this->getDado('exercicio')."'
        ";

        return $stSql;
    }

    public function recuperaFuncaoCreditosClassificados(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaFuncaoCreditosClassificados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaFuncaoCreditosClassificados()
    {
        $stSql = "
                    select tesouraria.fn_verifica_credito
                        (     " . $this->getDado("cod_credito") ."
                             ," . $this->getDado("cod_especie") ."
                             ," . $this->getDado("cod_genero") ."
                             ," . $this->getDado("cod_natureza") ."
                        )
                      as credito_classificado
        ";

        return $stSql;
    }

    public function recuperaFuncaoCreditosAcrescimos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaFuncaoCreditosAcrescimos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaFuncaoCreditosAcrescimos()
    {
        $stSql = "
                    select tesouraria.fn_verifica_acrescimo
                        (     " . $this->getDado("cod_credito") ."
                             ," . $this->getDado("cod_especie") ."
                             ," . $this->getDado("cod_genero") ."
                             ," . $this->getDado("cod_natureza") ."
                             ," . $this->getDado("cod_acrescimo") ."
                             ," . $this->getDado("cod_tipo") ."
                        )
                      as acrescimo_classificado
        ";

        return $stSql;
    }

}
