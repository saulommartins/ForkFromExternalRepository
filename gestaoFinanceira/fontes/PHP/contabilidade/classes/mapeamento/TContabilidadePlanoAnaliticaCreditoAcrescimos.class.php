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
    * Classe de mapeamento da tabela CONTABILIDADE.PLANO_ANALITICA_CREDITO
    * Data de CriaÃ§Ã£o: 12/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.02.02
                    uc-02.04.03
*/
require_once CLA_PERSISTENTE;

/**
  * Efetua conexÃ£o com a tabela  CONTABILIDADE.PLANO_ANALITICA_CREDITO
  * Data de CriaÃ§Ã£o: 12/09/2005

  * @author Analista: Lucas Leusin
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadePlanoAnaliticaCreditoAcrescimos extends Persistente
{
/**
    * MÃ©todo Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('contabilidade.plano_analitica_credito_acrescimo');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_especie,cod_genero,cod_natureza,cod_credito,cod_acrescimo,cod_tipo');

    $this->AddCampo( 'cod_plano'    ,'integer',true,  '', true,true );
    $this->AddCampo( 'exercicio'    ,   'char',true,'04', true,true );
    $this->AddCampo( 'cod_especie'  ,'integer',true,  '',false,true );
    $this->AddCampo( 'cod_genero'   ,'integer',true,  '',false,true );
    $this->AddCampo( 'cod_natureza' ,'integer',true,  '',false,true );
    $this->AddCampo( 'cod_credito'  ,'integer',true,  '',false,true );
    $this->AddCampo( 'cod_acrescimo','integer',true,  '',false,true );
    $this->AddCampo( 'cod_tipo'     ,'integer',true,  '',false,true );

}

/**
 * Valida credito/acrescimo, verificando se ja não esta vinculado a outra receita/conta
 */
function recuperaClassReceitasCreditosValidacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaClassReceitasCreditosValidacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaClassReceitasCreditosValidacao()
{
   $stSql  = "  SELECT CASE WHEN (orcamentaria.cod_credito IS NOT NULL) THEN                  ";
   $stSql .= "                    orcamentaria.cod_credito                                    ";
   $stSql .= "         ELSE                                                                   ";
   $stSql .= "                    extra.cod_credito                                           ";
   $stSql .= "         END AS cod_credito                                                     ";
   $stSql .= "   FROM monetario.credito                                                       ";
   $stSql .= "   LEFT JOIN (SELECT                                                            ";
   $stSql .= "               SUM(cod_credito) as cod_credito                                  ";
   $stSql .= "              ,cod_natureza                                                     ";
   $stSql .= "              ,cod_genero                                                       ";
   $stSql .= "              ,cod_especie                                                      ";
   $stSql .= "              FROM orcamento.receita_credito_acrescimo                          ";
   $stSql .= "              WHERE cod_credito = " . $this->getDado('cod_credito') ."          ";
   $stSql .= "                AND cod_especie = " . $this->getDado('cod_especie') ."          ";
   $stSql .= "                AND cod_genero = " . $this->getDado('cod_genero') ."            ";
   $stSql .= "                AND cod_natureza = " . $this->getDado('cod_natureza') ."        ";
   $stSql .= "                AND cod_acrescimo = " . $this->getDado('cod_acrescimo') ."      ";
   $stSql .= "                AND cod_tipo = " . $this->getDado('cod_tipo') ."                ";
   $stSql .= "                AND cod_receita != " . $this->getDado('codigo'). "              ";
   $stSql .= "                AND exercicio = '" . $this->getDado( 'exercicio' ) . "'         ";
   $stSql .= "              GROUP BY                                                          ";
   $stSql .= "                cod_natureza                                                    ";
   $stSql .= "               ,cod_genero                                                      ";
   $stSql .= "               ,cod_especie                                                     ";
   $stSql .= "              ) AS orcamentaria ON (                                            ";
   $stSql .= "                monetario.credito.cod_credito  = orcamentaria.cod_credito  AND  ";
   $stSql .= "                monetario.credito.cod_natureza = orcamentaria.cod_natureza AND  ";
   $stSql .= "                monetario.credito.cod_genero   = orcamentaria.cod_genero   AND  ";
   $stSql .= "                monetario.credito.cod_especie  = orcamentaria.cod_especie       ";
   $stSql .= "             )                                                                  ";
   $stSql .= "  LEFT JOIN (SELECT                                                             ";
   $stSql .= "               SUM(cod_credito) as cod_credito                                  ";
   $stSql .= "              ,cod_natureza                                                     ";
   $stSql .= "              ,cod_genero                                                       ";
   $stSql .= "              ,cod_especie                                                      ";
   $stSql .= "              FROM contabilidade.plano_analitica_credito_acrescimo              ";
   $stSql .= "              WHERE cod_credito = " . $this->getDado('cod_credito') ."          ";
   $stSql .= "                AND cod_especie = " . $this->getDado('cod_especie') ."          ";
   $stSql .= "                AND cod_genero = " . $this->getDado('cod_genero') ."            ";
   $stSql .= "                AND cod_natureza = " . $this->getDado('cod_natureza') ."        ";
   $stSql .= "                AND cod_acrescimo = " . $this->getDado('cod_acrescimo') ."      ";
   $stSql .= "                AND cod_tipo = " . $this->getDado('cod_tipo') ."                ";
   $stSql .= "                AND exercicio = '" . $this->getDado( 'exercicio' ) . "'         ";
   $stSql .= "              GROUP BY                                                          ";
   $stSql .= "                cod_natureza                                                    ";
   $stSql .= "               ,cod_genero                                                      ";
   $stSql .= "               ,cod_especie                                                     ";
   $stSql .= "              ) AS extra ON (                                                   ";
   $stSql .= "              monetario.credito.cod_credito  = extra.cod_credito AND            ";
   $stSql .= "              monetario.credito.cod_natureza = extra.cod_natureza AND           ";
   $stSql .= "              monetario.credito.cod_genero   = extra.cod_genero  AND            ";
   $stSql .= "              monetario.credito.cod_especie  = extra.cod_especie                ";
   $stSql .= "              )                                                                 ";
   $stSql .= "              WHERE                                                             ";
   $stSql .= "             monetario.credito.cod_credito  = ".$this->getDado('cod_credito')." ";
   $stSql .= "        AND  monetario.credito.cod_natureza = ".$this->getDado('cod_natureza')."";
   $stSql .= "        AND  monetario.credito.cod_genero   = ".$this->getDado('cod_genero')."  ";
   $stSql .= "        AND  monetario.credito.cod_especie  = ".$this->getDado('cod_especie')." ";
   $stSql .= "        AND  ( (orcamentaria.cod_credito is not null)  OR                       ";
   $stSql .= "               (extra.cod_credito is not null ) )                               ";

   return $stSql;
}
}
