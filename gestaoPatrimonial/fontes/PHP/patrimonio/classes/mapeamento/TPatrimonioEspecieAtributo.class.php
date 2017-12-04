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
include_once ( CLA_PERSISTENTE_ATRIBUTOS );

class TPatrimonioEspecieAtributo extends PersistenteAtributos
{
/**
    * Método Construtor
    * @access Private
*/
    public function TPatrimonioEspecieAtributo()
    {
        parent::PersistenteAtributos();
        $this->setTabela('patrimonio.especie_atributo');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_modulo,cod_cadastro,cod_atributo,cod_especie,cod_natureza,cod_grupo');
        $this->AddCampo('cod_modulo','integer',true,'',true,true);
        $this->AddCampo('cod_cadastro','integer',true,'',true,true);
        $this->AddCampo('cod_atributo','integer',true,'',true,true);
        $this->AddCampo('cod_natureza','integer',true,'',true,true);
        $this->AddCampo('cod_grupo','integer',true,'',true,true);
        $this->AddCampo('cod_especie','integer',true,'',true,true);
        $this->AddCampo('ativo','boolean',true,'true',false,false);

    }

    public function recuperaEspecieAtributo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaEspecieAtributo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEspecieAtributo()
    {
        $stSql .= "
            SELECT cod_modulo
                 , cod_cadastro
                 , cod_atributo
                 , cod_especie
                 , cod_natureza
                 , cod_grupo
              FROM patrimonio.especie_atributo
             WHERE ";
        if ( $this->getDado( 'cod_modulo' ) ) {
            $stSql .= " cod_modulo = ".$this->getDado( 'cod_modulo' )."  AND ";
        }
        if ( $this->getDado( 'cod_cadastro' ) ) {
            $stSql .= " cod_cadastro = ".$this->getDado( 'cod_cadastro' )."  AND ";
        }
        if ( $this->getDado( 'cod_atributo' ) ) {
            $stSql .= " cod_atributo = ".$this->getDado( 'cod_atributo' )."  AND ";
        }
        if ( $this->getDado( 'cod_especie' ) ) {
            $stSql .= " cod_especie = ".$this->getDado( 'cod_especie' )."  AND ";
        }
        if ( $this->getDado( 'cod_natureza' ) ) {
            $stSql .= " cod_natureza = ".$this->getDado( 'cod_natureza' )."  AND ";
        }
        if ( $this->getDado( 'cod_grupo' ) ) {
            $stSql .= " cod_grupo = ".$this->getDado( 'cod_grupo' )."  AND ";
        }
        if ( $this->getDado( 'nom_especie' ) ) {
            $stSql .= " nom_especie = '".$this->getDado( 'nom_especie' )."'  AND ";
        }
        if ( $this->getDado( 'ativo' ) ) {
            $stSql .= " ativo = '".$this->getDado( 'ativo' )."'  AND ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaInfoAtributos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stFiltroGroupBy = $stFiltro."
         GROUP BY atributo_dinamico.cod_modulo
                , atributo_dinamico.cod_cadastro
                , atributo_dinamico.cod_atributo
                , atributo_dinamico.cod_tipo
                , atributo_dinamico.nao_nulo
                , atributo_dinamico.nom_atributo
                , atributo_dinamico.valor_padrao
                , atributo_dinamico.ajuda
                , atributo_dinamico.mascara
                , atributo_dinamico.ativo
                , atributo_dinamico.interno
                , atributo_dinamico.indexavel";

        return $this->executaRecupera("montaRecuperaInfoAtributos",$rsRecordSet,$stFiltroGroupBy,$stOrder,$boTransacao);
    }

    public function montaRecuperaInfoAtributos()
    {
        $stSql = "
           SELECT atributo_dinamico.cod_modulo
                , atributo_dinamico.cod_cadastro
                , atributo_dinamico.cod_atributo
                , atributo_dinamico.cod_tipo
                , atributo_dinamico.nao_nulo
                , atributo_dinamico.nom_atributo
                , atributo_dinamico.valor_padrao
                , atributo_dinamico.ajuda
                , atributo_dinamico.mascara
                , atributo_dinamico.ativo
                , atributo_dinamico.interno
                , atributo_dinamico.indexavel
             FROM administracao.atributo_dinamico
       INNER JOIN patrimonio.especie_atributo
               ON especie_atributo.cod_modulo   = atributo_dinamico.cod_modulo
              AND especie_atributo.cod_cadastro = atributo_dinamico.cod_cadastro
              AND especie_atributo.cod_atributo = atributo_dinamico.cod_atributo
       INNER JOIN (
                    SELECT cod_modulo
                         , cod_cadastro
                         , cod_atributo
                         , cod_especie
                         , cod_natureza
                         , cod_grupo
                      FROM patrimonio.bem_atributo_especie
                  GROUP BY cod_modulo
                         , cod_cadastro
                         , cod_atributo
                         , cod_especie
                         , cod_natureza
                         , cod_grupo
                  ) AS bem_atributo_especie
               ON especie_atributo.cod_modulo   = bem_atributo_especie.cod_modulo
              AND especie_atributo.cod_cadastro = bem_atributo_especie.cod_cadastro
              AND especie_atributo.cod_atributo = bem_atributo_especie.cod_atributo
              AND especie_atributo.cod_especie  = bem_atributo_especie.cod_especie
              AND especie_atributo.cod_natureza = bem_atributo_especie.cod_natureza
              AND especie_atributo.cod_grupo    = bem_atributo_especie.cod_grupo
            WHERE atributo_dinamico.ativo = TRUE
        ";

        return $stSql;
    }

}
