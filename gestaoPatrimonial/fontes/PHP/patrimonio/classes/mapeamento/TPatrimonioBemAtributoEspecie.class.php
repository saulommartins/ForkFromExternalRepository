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
    * Data de Criação: 06/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25536 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-18 12:11:18 -0300 (Ter, 18 Set 2007) $

    * Casos de uso: uc-03.01.04
*/

/*
$Log$
Revision 1.1  2007/09/18 15:10:55  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php");
include_once ( CLA_PERSISTENTE);

class TPatrimonioBemAtributoEspecie extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
    public function TPatrimonioBemAtributoEspecie()
    {
        parent::PersistenteAtributosValores();
        $this->setPersistenteAtributo( new TPatrimonioEspecieAtributo );
        $this->setTabela('patrimonio.bem_atributo_especie');
        $this->setCampoCod('');
        $this->setComplementoChave('cod_bem,cod_modulo,cod_cadastro,cod_atributo,cod_especie,cod_natureza,cod_grupo');
        $this->AddCampo('cod_bem','integer',true,'',true,true);
        $this->AddCampo('cod_modulo','integer',true,'',true,true);
        $this->AddCampo('cod_cadastro','integer',true,'',true,true);
        $this->AddCampo('cod_atributo','integer',true,'',true,true);
        $this->AddCampo('cod_natureza','integer',true,'',true,true);
        $this->AddCampo('cod_grupo','integer',true,'',true,true);
        $this->AddCampo('cod_especie','integer',true,'',true,true);
        $this->AddCampo('valor','varchar',true,'',false,false);

    }

    public function recuperaAtributosValores(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAtributosValores($stFiltro, $stOrdem);
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaAtributosValores($stFiltro, $stOrdem)
    {
        $stSql = " SELECT bem_atributo_especie.cod_modulo
                        , bem_atributo_especie.cod_cadastro
                        , bem_atributo_especie.cod_atributo
                        , bem_atributo_especie.cod_especie
                        , bem_atributo_especie.cod_grupo
                        , atributo_valor_padrao.cod_valor
                        , atributo_valor_padrao.valor_padrao
                     FROM patrimonio.bem_atributo_especie
               INNER JOIN administracao.atributo_valor_padrao
                       ON bem_atributo_especie.cod_modulo = atributo_valor_padrao.cod_modulo
                      AND bem_atributo_especie.cod_cadastro = atributo_valor_padrao.cod_cadastro
                      AND bem_atributo_especie.cod_atributo = atributo_valor_padrao.cod_atributo
                      ".$stFiltro."
                 GROUP BY bem_atributo_especie.cod_modulo
                        , bem_atributo_especie.cod_cadastro
                        , bem_atributo_especie.cod_atributo
                        , bem_atributo_especie.cod_especie
                        , bem_atributo_especie.cod_grupo
                        , atributo_valor_padrao.cod_valor
                        , atributo_valor_padrao.valor_padrao
                        ".$stOrdem;

        return $stSql;
    }
}
