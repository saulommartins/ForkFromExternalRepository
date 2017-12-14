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
    * Classe de mapeamento da tabela DIVIDA.MODALIDADE_REDUCAO
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATModalidadeReducao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.07
*/

/*
$Log$
Revision 1.5  2007/07/20 20:55:11  cercato
correcao para exclusao de modalidade.

Revision 1.4  2007/04/24 15:13:04  cercato
alteracao na funcao de lista da reducao.

Revision 1.3  2007/02/09 18:29:13  cercato
correcoes para divida.cobranca

Revision 1.2  2006/10/05 15:03:37  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/09/25 14:54:56  cercato
classes de mapeamento para funcionamento da modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATModalidadeReducao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATModalidadeReducao()
    {
        parent::Persistente();
        $this->setTabela('divida.modalidade_reducao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_modalidade');

        $this->AddCampo('cod_modalidade','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,true);
        $this->AddCampo('cod_funcao','integer',true,'',true,true);
        $this->AddCampo('cod_biblioteca','integer',true,'',true,true);
        $this->AddCampo('cod_modulo','integer',true,'',true,true);

        $this->AddCampo('percentual','boolean',true,'',true,false);
        $this->AddCampo('valor','numeric',true,'14,2',true,false);
    }

    public function recuperaListaReducao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaReducao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaReducao()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     af.nom_funcao, \n";
        $stSql .= "     dmr.cod_modulo, \n";
        $stSql .= "     dmr.cod_biblioteca, \n";
        $stSql .= "     dmr.cod_funcao AS cod_funcao_r, \n";
        $stSql .= "     dmr.cod_modulo ||'.'|| dmr.cod_biblioteca ||'.'|| dmr.cod_funcao AS cod_funcao, \n";
        $stSql .= "     dmr.percentual, \n";
        $stSql .= "     dmr.valor \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.modalidade_reducao AS dmr \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     administracao.funcao AS af \n";
        $stSql .= " ON \n";
        $stSql .= "     af.cod_modulo = dmr.cod_modulo \n";
        $stSql .= "     AND af.cod_biblioteca = dmr.cod_biblioteca \n";
        $stSql .= "     AND af.cod_funcao = dmr.cod_funcao \n";

        return $stSql;
    }

    public function recuperaListaReducaoTipo(&$rsRecordSet, $inCodModalidade, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaReducaoTipo($inCodModalidade);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaReducaoTipo($inCodModalidade)
    {
        $stSql  = "
            SELECT 'C' AS tipo_reducao
                 , NULL AS cod_tipo
                 , NULL AS cod_acrescimo
                 , modalidade_reducao_credito.cod_credito ||'.'|| modalidade_reducao_credito.cod_especie ||'.'|| modalidade_reducao_credito.cod_genero ||'.'|| modalidade_reducao_credito.cod_natureza AS credito
                 , modalidade_reducao_credito.percentual
                 , modalidade_reducao_credito.valor
              FROM divida.modalidade_reducao_credito
        INNER JOIN divida.modalidade
                ON modalidade.cod_modalidade    = modalidade_reducao_credito.cod_modalidade
               AND modalidade.ultimo_timestamp  = modalidade_reducao_credito.timestamp
             WHERE modalidade.cod_modalidade = ".$inCodModalidade."
             UNION SELECT 'A' AS tipo_reducao
                        , modalidade_reducao_acrescimo.cod_tipo
                        , modalidade_reducao_acrescimo.cod_acrescimo
                        , NULL AS credito
                        , modalidade_reducao_acrescimo.percentual
                        , modalidade_reducao_acrescimo.valor
                     FROM divida.modalidade_reducao_acrescimo
               INNER JOIN divida.modalidade
                       ON modalidade.cod_modalidade     = modalidade_reducao_acrescimo.cod_modalidade
                      AND modalidade.ultimo_timestamp   = modalidade_reducao_acrescimo.timestamp
                    WHERE case when modalidade_reducao_acrescimo.pagamento = true then
                            false
                          else
                            true
                          end
                      and modalidade.cod_modalidade = ".$inCodModalidade;

        return $stSql;
    }
}// end of class

?>
