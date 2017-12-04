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
    * Classe de mapeamento da tabela DIVIDA.MODALIDADE_CREDITO
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATModalidadeCredito.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.07
*/

/*
$Log$
Revision 1.4  2007/07/20 20:55:11  cercato
correcao para exclusao de modalidade.

Revision 1.3  2007/02/09 18:28:54  cercato
correcoes para divida.cobranca

Revision 1.2  2006/10/05 15:01:22  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/09/25 14:54:56  cercato
classes de mapeamento para funcionamento da modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATModalidadeCredito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATModalidadeCredito()
    {
        parent::Persistente();
        $this->setTabela('divida.modalidade_credito');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_modalidade');

        $this->AddCampo('cod_modalidade','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,true);
        $this->AddCampo('cod_especie','integer',true,'',true,true);
        $this->AddCampo('cod_genero','integer',true,'',true,true);
        $this->AddCampo('cod_natureza','integer',true,'',true,true);
        $this->AddCampo('cod_credito','integer',true,'',true,true);
    }

    public function recuperaListaCredito(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaCredito().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaCredito()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     mc.descricao_credito AS descricao, \n";
        $stSql .= "     LPAD(dmc.cod_credito::VARCHAR,3,'0') ||'.'|| LPAD(dmc.cod_especie::VARCHAR,3,'0') ||'.'|| LPAD(dmc.cod_genero::VARCHAR,2,'0') ||'.'|| dmc.cod_natureza AS codcredito \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.modalidade_credito AS dmc \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     monetario.credito AS mc \n";
        $stSql .= " ON \n";
        $stSql .= "     mc.cod_credito = dmc.cod_credito \n";
        $stSql .= "     AND mc.cod_natureza = dmc.cod_natureza \n";
        $stSql .= "     AND mc.cod_genero = dmc.cod_genero \n";
        $stSql .= "     AND mc.cod_especie = dmc.cod_especie \n";

        return $stSql;
    }

}// end of class

?>
