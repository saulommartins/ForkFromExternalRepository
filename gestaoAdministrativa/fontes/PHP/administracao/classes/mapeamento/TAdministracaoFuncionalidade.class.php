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
    * Classe de mapeamento da tabela ADMINISTRACAO.FUNCIONALIDADE
    * Data de Criação: 03/11/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 6707 $
    $Name$
    $Author: domluc $
    $Date: 2006-03-02 17:20:11 -0300 (Qui, 02 Mar 2006) $

    * Casos de uso: uc-01.03.91
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.FUNCIONALIDADE
  * Data de Criação: 03/11/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoFuncionalidade extends Persistente
{
    public function TAdministracaoFuncionalidade()
    {
        parent::Persistente();
        $this->setTabela('ADMINISTRACAO.FUNCIONALIDADE');
        $this->setCampoCod('cod_funcionalidade');

        $this->AddCampo('cod_funcionalidade', 'integer', true, '',   true,  false );
        $this->AddCampo('cod_modulo',         'integer', true, '',   false, true  );
        $this->AddCampo('nom_funcionalidade', 'varchar', true, '40', false, false );
        $this->AddCampo('nom_diretorio',      'varchar', true, '40', false, false );
        $this->AddCampo('ordem' ,             'integer', true, '',   false, false );
    }

    public function montaRecuperaRelacionamento()
    {
        $sSQL  = " SELECT                                                \n";
        $sSQL .= "     f.ordem,                                          \n";
        $sSQL .= "     f.cod_funcionalidade,                             \n";
        $sSQL .= "     f.nom_funcionalidade,                             \n";
        $sSQL .= "     m.nom_modulo                                      \n";
        $sSQL .= " FROM                                                  \n";
        $sSQL .= "     administracao.modulo as m,                        \n";
        $sSQL .= "     administracao.funcionalidade as f,                \n";
        $sSQL .= "     (                                                 \n";
        $sSQL .= "     SELECT                                            \n";
        $sSQL .= "         a.cod_funcionalidade                          \n";
        $sSQL .= "     FROM                                              \n";
        $sSQL .= "         administracao.acao as a,                      \n";
        $sSQL .= "         administracao.permissao as p                  \n";
        $sSQL .= "     WHERE                                             \n";
        $sSQL .= "         a.cod_acao = p.cod_acao AND                   \n";
        $sSQL .= "         p.numcgm = ".Sessao::read('numCgm')." AND            \n";
        $sSQL .= "         p.ano_exercicio = ".Sessao::getExercicio()."      \n";
        $sSQL .= "     GROUP BY                                          \n";
        $sSQL .= "          a.cod_funcionalidade                         \n";
        $sSQL .= "     ) AS a                                            \n";
        $sSQL .= " WHERE                                                 \n";
        $sSQL .= "     m.cod_modulo = f.cod_modulo AND                   \n";
        $sSQL .= "     f.cod_funcionalidade = a.cod_funcionalidade AND   \n";
        //$sSQL .= "     f.cod_modulo = ".$cod_modulo_pass."               \n";
        //$sSQL .= " ORDER By                                              \n";
        //$sSQL .= "     f.ordem                                           \n";
        return $stSQL;
    }
}
