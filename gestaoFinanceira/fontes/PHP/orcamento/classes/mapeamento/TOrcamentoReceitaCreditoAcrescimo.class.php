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
    * Classe de mapeamento da tabela ORCAMENTO.RECEITA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.06, uc-02.04.04, uc-02.01.34, uc-02.04.03
*/

/*
$Log$
Revision 1.3  2007/08/13 18:47:44  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.2  2007/06/14 13:52:11  domluc
Ajustes dos Casos de Uso

Revision 1.1  2007/05/29 14:12:35  domluc
2 novas tabelas necessarias para as mudanças na Classif. de Receitas

Revision 1.22  2007/01/18 12:39:35  andre.almeida
Atualizado.

Revision 1.21  2007/01/18 11:05:52  andre.almeida
Adicionado método recuperaPrevisaoReceita.

Revision 1.20  2006/10/24 18:37:34  bruce
Bug #7201#

Revision 1.19  2006/10/06 18:16:38  cako
Bug #7027#

Revision 1.18  2006/09/25 12:09:44  cleisson
Bug #7032#

Revision 1.17  2006/09/05 15:35:23  rodrigo
Caso de uso 02.01.34

Revision 1.16  2006/08/30 08:52:36  rodrigo
*** empty log message ***

Revision 1.15  2006/08/18 19:33:08  eduardo
Bug #5238#
Bug #5239#

Revision 1.14  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.RECEITA
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoReceitaCreditoAcrescimo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TOrcamentoReceitaCreditoAcrescimo()
    {
        parent::Persistente();

        $this->setTabela('orcamento.receita_credito_acrescimo');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_tipo, cod_acrescimo, cod_credito, cod_natureza, cod_genero, cod_especie, exercicio');

        $this->AddCampo('cod_tipo','integer',true,'',true,true);
        $this->AddCampo('cod_acrescimo','integer',true,'',true,true);

        $this->AddCampo('cod_credito','integer',true,'',true,true);
        $this->AddCampo('cod_especie','integer',true,'',true,true);
        $this->AddCampo('cod_genero','integer',true,'',true,true);
        $this->AddCampo('cod_natureza','integer',true,'',true,true);

        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('cod_receita','integer',true,'',true,false);
        $this->AddCampo('divida_ativa'         ,'boolean' ,true, '' , true, true);
    }

    /**
     * Método que retorna as receitas vinculadas a cada credito de acrescimo
     *
     * @author    Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param recordset $rsRecordSet
     * @param string    $stFiltro    Filtros alternativos que podem ser passados
     * @param string    $stOrder     Ordenacao do SQL
     * @param boolean   $boTransacao Usar transacao
     *
     * @return recordset
     */
    public function recuperaReceitaCreditoAcrescimo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReceitaCreditoAcrescimo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Método que retorna as receitas vinculadas a cada credito de acrescimo
     *
     * @author    Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return string
     */
     function montaRecuperaReceitaCreditoAcrescimo()
     {
         $stSql = "
            SELECT receita_credito_acrescimo.exercicio
                 , receita_credito_acrescimo.cod_receita
                 , conta_receita.descricao
              FROM orcamento.receita_credito_acrescimo
        INNER JOIN orcamento.receita
                ON receita.exercicio = receita_credito_acrescimo.exercicio
               AND receita.cod_receita = receita_credito_acrescimo.cod_receita
        INNER JOIN orcamento.conta_receita
                ON conta_receita.exercicio = receita.exercicio
               AND conta_receita.cod_conta = receita.cod_conta
             WHERE ";

         if ($this->getDado('cod_tipo') != '') {
             $stSql .= "receita_credito_acrescimo.cod_tipo = ".$this->getDado('cod_tipo')." AND  ";
         }
         if ($this->getDado('cod_acrescimo') != '') {
             $stSql .= "receita_credito_acrescimo.cod_acrescimo = ".$this->getDado('cod_acrescimo')." AND  ";
         }
         if ($this->getDado('cod_credito') != '') {
             $stSql .= "receita_credito_acrescimo.cod_credito = ".$this->getDado('cod_credito')." AND  ";
         }
         if ($this->getDado('cod_natureza') != '') {
             $stSql .= "receita_credito_acrescimo.cod_natureza = ".$this->getDado('cod_natureza')." AND  ";
         }
         if ($this->getDado('cod_genero') != '') {
             $stSql .= "receita_credito_acrescimo.cod_genero = ".$this->getDado('cod_genero')." AND  ";
         }
         if ($this->getDado('cod_especie') != '') {
             $stSql .= "receita_credito_acrescimo.cod_especie = ".$this->getDado('cod_especie')." AND  ";
         }
         if ($this->getDado('exercicio') != '') {
             $stSql .= "receita_credito_acrescimo.exercicio = ".$this->getDado('exercicio')." AND  ";
         }
         if ($this->getDado('divida_ativa') != '') {
             $stSql .= "receita_credito_acrescimo.divida_ativa = '".$this->getDado('divida_ativa')."' AND  ";
         }

         return substr($stSql,0,-6);
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
   $stSql .= " LEFT JOIN (SELECT                                                              ";
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
   $stSql .= "                AND divida_ativa = '" . $this->getDado( 'divida_ativa' ) . "'   ";
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
   $stSql .= " LEFT JOIN (SELECT                                                              ";
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
   $stSql .= "              monetario.credito.cod_credito  = ".$this->getDado('cod_credito')."";
   $stSql .= "         AND  monetario.credito.cod_natureza = ".$this->getDado('cod_natureza')."";
   $stSql .= "         AND  monetario.credito.cod_genero   = ".$this->getDado('cod_genero')." ";
   $stSql .= "         AND  monetario.credito.cod_especie  = ".$this->getDado('cod_especie')."";
   $stSql .= "         AND  ( (orcamentaria.cod_credito is not null)  OR                      ";
   $stSql .= "               (extra.cod_credito is not null ) )                               ";

   return $stSql;
}
}
