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
    * Classe de mapeamento da tabela stn.recurso_rreo_anexo_14
    * Data de Criação: 18/06/2008

    * @author Analista: Tonismar Regis Bernardo
    * @author Desenvolvedor: Henrique Boaventura

    * Casos de uso: uc-06.01.14

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TSTNRecursoRREOAnexo14 extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TSTNRecursoRREOAnexo14()
    {
        parent::Persistente();

        $this->setTabela('stn.recurso_rreo_anexo_14');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_recurso');

        $this->AddCampo( 'exercicio'    ,'char'   ,true , '04',true ,true  );
        $this->AddCampo( 'cod_recurso'  ,'integer',true ,   '',true ,true  );

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT recurso_rreo_anexo_14.exercicio
                 , recurso_rreo_anexo_14.cod_recurso
                 , recurso.nom_recurso
              FROM stn.recurso_rreo_anexo_14
        INNER JOIN orcamento.recurso
                ON recurso.exercicio = recurso_rreo_anexo_14.exercicio
               AND recurso.cod_recurso = recurso_rreo_anexo_14.cod_recurso
              WHERE recurso_rreo_anexo_14.exercicio = '".$this->getDado('exercicio')."'

        ";

        return $stSql;
    }

}
?>
