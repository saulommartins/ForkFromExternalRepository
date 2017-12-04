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
 * Enforça anotações de classes
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 */

/**
 * Classe Exceção de Anotações
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage LDO
 */
class AnotacaoExcecao extends Exception
{
}

/**
 * Classe Abstrata de Anotações
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage LDO
 */
abstract class LDOAnotacoes
{
    private $arAnotacoes = array();

    /**
     * Recuperar autor da classe ou método no texto de documentação.
     * @param  string $stDoc texto de documentação
     * @return string
     */
    public function recuperarAutor($stDoc)
    {
        if (preg_match('/@author\s+((?:\S+\s*?)*?)$/im', $stDoc, $arAutor)) {
            return array_pop($arAutor);
        }

        return null;
    }

    /**
     * Recuperar nome de usuário do autor da classe ou do método no texto.
     * de documentação.
     * @param  string $stDoc texto de documentação
     * @return string
     */
    public function recuperarUsuario($stDoc)
    {
        $stAutor = $this->recuperarAutor($stDoc);

        if ($stAutor) {
            $i = strpos($stAutor, '<');

            if ($i !== false) {
                $j = strpos($stAutor, '@');

                if ($j === false) {
                    $j = strpos($stAutor, '>');
                }

                if ($j !== false) {
                    $i++;

                    return substr($stAutor, $i, $j - $i);
                }
            }
        }

        return null;
    }

    /**
     * recuperar pacote da classe ou método no texto de documentação.
     * @param  string $stDoc texto de documentação
     * @return string
     */
    public function recuperarPacote($stDoc)
    {
        if (preg_match('/@package\s+((?:\S+\s*?)*?)$/im', $stDoc, $arPacote)) {
            return array_pop($arPacote);
        }

        return null;
    }

    /**
     * Recuperar subpacote da classe ou método no texto de documentação.
     * @param  string $stDoc texto de documentação
     * @return string
     */
    public function recuperarSubpacote($stDoc)
    {
        if (preg_match('/@subpackage\s+((?:\S+\s*?)*?)$/im', $stDoc, $arSubpacote)) {
            return array_pop($arSubpacote);
        }

        return null;
    }

    /**
     * Recuperar caso de uso da classe ou método no texto de documentação.
     * @param  string $stDoc texto de documentação
     * @return string
     */
    public function recuperarUC($stDoc)
    {
        if (preg_match('/@uc\s+((?:\S+\s*?)*?)$/im', $stDoc, $arUC)) {
            $stDoc = array_pop($arUC);

            if (preg_match('/^(\d{2}\.\d{2}\.\d{2}\s+-\s+.*?)$/i', $stDoc, $arUC)) {
                return array_pop($arUC);
            }

            return false;
        }

        return false;
    }

    /**
     * Testa se a classe possui os campos autor, pacote, subpacote e caso
     * de uso.
     * @param string $stClasse nome da classe filha
     */
    final protected function validarClasse($stClasse)
    {
        if (constant('ENV_TYPE') == 'dev') {
            $obClasse = new ReflectionClass($stClasse);
            $stPHPDoc = $obClasse->getDocComment();

            $stAutor = $this->recuperarAutor($stPHPDoc);
            if (!$stAutor) {
                throw new AnotacaoExcecao("tag PHPDoc @author faltando no comentário da classe '$stClasse'");
            }

            $stUsuario = $this->recuperarUsuario($stPHPDoc);
            if (!$stUsuario) {
                throw new AnotacaoExcecao("tag PHPDoc @author faltando campo usuário entre <> no comentário da classe '$stClasse'");
            }

            $stPacote = $this->recuperarPacote($stPHPDoc);
            if (!$stPacote) {
                throw new AnotacaoExcecao("tag PHPDoc @package faltando no comentário da classe '$stClasse'");
            }

            $stSubpacote = $this->recuperarSubpacote($stPHPDoc);
            if (!$stSubpacote) {
                throw new AnotacaoExcecao("tag PHPDoc @subpackage faltando no comentário da classe '$stClasse'");
            }

            $stUC = $this->recuperarUC($stPHPDoc);
            if ($stUC === false) {
                throw new AnotacaoExcecao("formato errado para tag PHPDoc @uc na classe '$stClasse', 'XX.YY.ZZ - Nome do Componente' esperado");
            } elseif (!$stUC) {
                throw new AnotacaoExcecao("tag PHPDoc @uc faltando no comentário da classe '$stClasse'");
            }

            $this->arAnotacoes = array('autor'      => $stAutor,
                                       'usuario'    => $stUsuario,
                                       'pacote'     => $stPacote,
                                       'subpacote'  => $stSubpacote,
                                       'uc'         => $stUC);
       }
    }

    /**
     * Retorna o valor das anotações do PHPDOC (autor, pacote, subpacote
     * e caso de uso).
     */
    public function recuperarAnotacoes()
    {
        return $this->arAnotacoes;
    }
}
