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
    * 
    * Data de Criação   : 16/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPERelacionamentoFonteRecurso.class.php 60382 2014-10-16 17:49:29Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPERelacionamentoFonteRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPERelacionamentoFonteRecurso()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT   
                             codigo_fonte_recurso.cod_fonte AS cod_font_recursos
                             , recurso.cod_recurso AS cod_font_ug
                             , recurso.nom_recurso AS descricao
                    FROM tcepe.codigo_fonte_recurso 
                    JOIN orcamento.recurso
                      ON recurso.cod_recurso=codigo_fonte_recurso.cod_recurso
                     AND recurso.exercicio=codigo_fonte_recurso.exercicio
                    JOIN tcepe.codigo_fonte_tce
                      ON codigo_fonte_tce.cod_fonte=codigo_fonte_recurso.cod_fonte
                    WHERE codigo_fonte_recurso.exercicio  = '".$this->getDado('exercicio')."'
        ";
        
        return $stSql;
    }
}

?>