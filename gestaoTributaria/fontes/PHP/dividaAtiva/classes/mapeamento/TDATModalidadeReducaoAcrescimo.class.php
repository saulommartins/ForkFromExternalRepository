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
    * Classe de mapeamento da tabela DIVIDA.MODALIDADE_REDUCAO_CREDITO
    * Data de Criação: 19/04/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATModalidadeReducaoAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.04
*/

/*
$Log$
Revision 1.3  2007/09/14 14:30:42  cercato
campo "pagamento" virou chave.

Revision 1.2  2007/07/20 20:55:11  cercato
correcao para exclusao de modalidade.

Revision 1.1  2007/04/24 15:13:49  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATModalidadeReducaoAcrescimo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATModalidadeReducaoAcrescimo()
    {
        parent::Persistente();
        $this->setTabela('divida.modalidade_reducao_acrescimo');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_modulo,cod_biblioteca,cod_funcao,cod_modalidade,timestamp,cod_acrescimo,cod_tipo,pagamento');

        $this->AddCampo('cod_modulo','integer',true,'',true,true);
        $this->AddCampo('cod_biblioteca','integer',true,'',true,true);
        $this->AddCampo('cod_funcao','integer',true,'',true,true);
        $this->AddCampo('cod_modalidade','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,true);
        $this->AddCampo('cod_acrescimo','integer',true,'',true,true);
        $this->AddCampo('cod_tipo','integer',true,'',true,true);
        $this->AddCampo('pagamento','boolean',true,'',true,true);

        $this->AddCampo('percentual','boolean',true,'',true,true);
        $this->AddCampo('valor','numeric',true,'14,2',true,true);
    }

    public function recuperaListaReducaoAcrescimo(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaReducaoAcrescimo().$stParametros;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaReducaoAcrescimo()
    {
        $stSql   = "SELECT
                        dm.cod_acrescimo AS valor,
                        (
                            SELECT
                                ma.descricao_acrescimo
                            FROM
                                monetario.acrescimo AS ma
                            WHERE
                                ma.cod_tipo = dm.cod_tipo
                                AND ma.cod_acrescimo = dm.cod_acrescimo
                        )AS descricao,
                        dm.cod_tipo

                    FROM
                        divida.modalidade_reducao_acrescimo AS dm
                    \n";

        return $stSql;
   }
}// end of class

?>
