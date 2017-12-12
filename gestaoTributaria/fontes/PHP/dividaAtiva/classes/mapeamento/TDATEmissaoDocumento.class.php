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
    * Classe de mapeamento da tabela DIVIDA.EMISSAO_DOCUMENTO
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATEmissaoDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.03
*/

/*
$Log$
Revision 1.8  2007/08/14 15:12:37  cercato
adicionando exercicio em funcao de alteracao na base de dados.

Revision 1.7  2007/04/24 18:28:27  cercato
adicionando campo num_documento

Revision 1.6  2007/03/26 15:31:55  cercato
alterando campo do timestamp para null

Revision 1.5  2007/02/09 18:28:24  cercato
correcoes para divida.cobranca

Revision 1.4  2006/10/06 17:04:19  dibueno
inserção das chaves da tabela

Revision 1.3  2006/10/05 14:59:47  dibueno
Alterações nas colunas da tabela

Revision 1.2  2006/10/04 09:35:44  dibueno
Adição de função para buscar numeracao

Revision 1.1  2006/09/29 10:49:39  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATEmissaoDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATEmissaoDocumento()
    {
        parent::Persistente();
        $this->setTabela('divida.emissao_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento');

        $this->AddCampo('num_parcelamento','integer',true,'',true,true);
        $this->AddCampo('num_emissao','integer',true,'',true,false);
        $this->AddCampo('cod_tipo_documento','integer',true,'',true,true);
        $this->AddCampo('cod_documento','integer',true,'',true,true);

        $this->AddCampo('num_documento','integer',true,'',true,false);
        $this->AddCampo('exercicio','varchar',true,'4',true,false);

        $this->AddCampo('timestamp','timestamp',false,'',false,false);
        $this->AddCampo('numcgm_usuario','integer',true,'',false,true);
    }

    public function recuperaNumeroEmissaoDocumento(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNumeroEmissaoDocumento();
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNumeroEmissaoDocumento()
    {
        $stSql  = " SELECT													\n";
        $stSql .= "		coalesce ( (max(ded.num_emissao)+1), 1 ) as valor	\n";
        $stSql .= "	FROM													\n";
        $stSql .= "		divida.emissao_documento as ded						\n";

        return $stSql;
    }

    public function recuperaNumeroDocumento(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNumeroDocumento();
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNumeroDocumento()
    {
        $stSql  = " SELECT                                                  \n";
        $stSql .= "     coalesce ( (max(ded.num_documento)+1), 1 ) as valor \n";
        $stSql .= " FROM                                                    \n";
        $stSql .= "     divida.emissao_documento as ded                     \n";

        return $stSql;
    }

}// end of class

?>
