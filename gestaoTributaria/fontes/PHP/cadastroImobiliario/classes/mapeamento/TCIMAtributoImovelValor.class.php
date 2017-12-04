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
     * Classe de mapeamento para a tabela IMOBILIARIO.ATRIBUTO_IMOVEL_VALOR
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMAtributoImovelValor.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.7  2007/03/01 12:53:56  rodrigo
Bug #8436#

Revision 1.6  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_ATRIBUTOS_VALORES );
//include_once    ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoCadastroImobiliario.class.php" );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.ATRIBUTO_IMOVEL_VALOR
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMAtributoImovelValor extends PersistenteAtributosValores
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMAtributoImovelValor()
    {
        parent::PersistenteAtributosValores();
        $this->setTabela('imobiliario.atributo_imovel_valor');
    //    $this->setPersistenteAtributo( new TCIMAtributoCadastroImobiliario );

        $this->setCampoCod('');
        $this->setComplementoChave('inscricao_municipal,cod_atributo,cod_cadastro,timestamp,cod_modulo');

        $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
        $this->AddCampo('cod_atributo','integer',true,'',true,true);
        $this->AddCampo('cod_cadastro','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,false);
        $this->AddCampo('valor','varchar',true,'500',false,false);
        $this->AddCampo('cod_modulo','interger',true,'',true,true);

    }

    public function recuperaImovelValor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaImovelValor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaImovelValor()
    {
        $stSql =" SELECT inscricao_municipal                                        \n";
        $stSql.="       ,cod_atributo                                               \n";
        $stSql.="       ,cod_cadastro                                               \n";
        $stSql.="       ,TO_CHAR(timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp \n";
        $stSql.="       ,valor                                                      \n";
        $stSql.="       ,cod_modulo                                                 \n";
        $stSql.="   FROM imobiliario.atributo_imovel_valor                          \n";

        return $stSql;
    }
}
