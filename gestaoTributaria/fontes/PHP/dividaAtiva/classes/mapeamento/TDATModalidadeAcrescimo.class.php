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
    * Classe de mapeamento da tabela DIVIDA.MODALIDADE_ACRESCIMO
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATModalidadeAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.07
*/

/*
$Log$
Revision 1.6  2007/09/14 14:30:42  cercato
campo "pagamento" virou chave.

Revision 1.5  2007/09/10 14:24:06  cercato
alteracao para modalidade acrescimo trabalhar com incidencia.

Revision 1.4  2007/07/20 20:55:11  cercato
correcao para exclusao de modalidade.

Revision 1.3  2007/02/09 18:28:24  cercato
correcoes para divida.cobranca

Revision 1.2  2006/10/05 14:59:47  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/09/25 14:54:56  cercato
classes de mapeamento para funcionamento da modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATModalidadeAcrescimo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATModalidadeAcrescimo()
    {
        parent::Persistente();
        $this->setTabela('divida.modalidade_acrescimo');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_modalidade');

        $this->AddCampo('cod_modalidade','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,true);
        $this->AddCampo('cod_tipo','integer',true,'',true,true);
        $this->AddCampo('cod_acrescimo','integer',true,'',true,true);
        $this->AddCampo('pagamento','boolean',true,'',true,false);
        $this->AddCampo('cod_funcao','integer',true,'',false,true);
        $this->AddCampo('cod_biblioteca','integer',true,'',false,true);
        $this->AddCampo('cod_modulo','integer',true,'',false,true);
    }

    public function recuperaListaAcrescimo(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaAcrescimo().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaAcrescimo()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     dma.cod_tipo, \n";
        $stSql .= "     dma.pagamento, \n";
        $stSql .= "     ma.descricao_acrescimo, \n";
        $stSql .= "     af.nom_funcao, \n";
        $stSql .= "     dma.cod_modulo ||'.'|| dma.cod_biblioteca ||'.'|| dma.cod_funcao AS cod_funcao, \n";
        $stSql .= "     dma.cod_acrescimo \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.modalidade_acrescimo AS dma \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     monetario.acrescimo AS ma \n";
        $stSql .= " ON \n";
        $stSql .= "     ma.cod_tipo = dma.cod_tipo \n";
        $stSql .= "     AND ma.cod_acrescimo = dma.cod_acrescimo \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     administracao.funcao AS af \n";
        $stSql .= " ON \n";
        $stSql .= "     af.cod_modulo = dma.cod_modulo \n";
        $stSql .= "     AND af.cod_biblioteca = dma.cod_biblioteca \n";
        $stSql .= "     AND af.cod_funcao = dma.cod_funcao \n";

        return $stSql;
    }

}// end of class

?>
