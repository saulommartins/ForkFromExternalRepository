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
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

$Id: cgmLegado.class.php 65466 2016-05-24 16:21:19Z evandro $

*/

class cgmLegado
{
    public $vetCgm, $altCgm, $vetCga, $vetAux, $vetPesq, $numCgm, $nomCgm, $tipoBusca, $boxCgm, $vetAtributo;
    public $stErro;
    /** Método Construtor **/
    public function cgmLegado()
    {
        $this->vetCgm    = "";
        $this->altCgm    = "";
        $this->vetCga    = "";
        $this->vetAux    = "";
        $this->vetPesq   = "";
        $this->numCgm    = "";
        $this->nomCgm    = "";
        $this->tipoBusca = "";
        $this->boxCgm    = "";
        $this->vetAtributo = array();
    }//Fim do método construtor

/***************************************************************************
Inclui um novo CGM
Recebe os dados do CGM através de um vetor e insere na tabela do CGM
Retorna bool
/**************************************************************************/
    public function incluiCgm($vetCgm)
    {
                //Monta a query com os dados do vetor
        $sql = "Insert Into sw_cgm (
                numcgm, cod_pais, cod_pais_corresp, cod_municipio, cod_uf, cod_municipio_corresp, cod_uf_corresp,
                nom_cgm, tipo_logradouro, logradouro, numero,  complemento, bairro, cep,
                tipo_logradouro_corresp, logradouro_corresp, numero_corresp,  complemento_corresp,
                bairro_corresp, cep_corresp, fone_residencial, ramal_residencial, fone_comercial, ramal_comercial,
                fone_celular, e_mail, e_mail_adcional, cod_responsavel, site
                ) Values('".$vetCgm['numCgm']."', '".$vetCgm['pais']."', '".$vetCgm['paisCorresp']."',
                '".$vetCgm['codMunicipio']."', '".$vetCgm['codUf']."',
                '".$vetCgm['codMunicipioCorresp']."', '".$vetCgm['codUfCorresp']."',
                '".str_replace('\'','\'\'',$vetCgm['nomCgm'])."', '".$vetCgm['stNomeTipoLogradouro']."', '".$vetCgm['stNomeLogradouro']."',
                '".$vetCgm['numero']."', '".$vetCgm['complemento']."',
                '".$vetCgm['stNomeBairro']."', '".$vetCgm['cep']."',
                '".$vetCgm['stNomeTipoLogradouroCorresp']."', '".$vetCgm['stNomeLogradouroCorresp']."',
                '".$vetCgm['numeroCorresp']."', '".$vetCgm['complementoCorresp']."',
                '".$vetCgm['stNomeBairroCorresp']."', '".$vetCgm['cepCorresp']."',
                '".$vetCgm['foneRes']."', '".$vetCgm['ramalRes']."', '".$vetCgm['foneCom']."', '".$vetCgm['ramalCom']."',
                '".$vetCgm['foneCel']."', '".$vetCgm['email']."', '".$vetCgm['emailAdic']."', '".$vetCgm['codResp']."', '".$vetCgm['site']."'
                ); ";
        
        $sql .= $this->montaIncluiAtributos( $vetCgm['numCgm'] );

        $sql .= "Insert into sw_cgm_logradouro ( numcgm, cod_uf, cod_municipio, cod_bairro, cod_logradouro, cep ) Values ('".$vetCgm['numCgm']."', '".$vetCgm['codUf']."', '".$vetCgm['codMunicipio']."', '".$vetCgm['bairro']."', '".$vetCgm['logradouro']."', '".$vetCgm['cep']."' ); ";

        if (( $vetCgm['logradouroCorresp'] != 0 ) and ( $vetCgm['bairroCorresp'] != 0 ) and ( $vetCgm['cepCorresp'] != 0 )) {
            $sql .= "Insert into sw_cgm_logradouro_correspondencia ( numcgm, cod_uf, cod_municipio, cod_bairro, cod_logradouro, cep ) Values ('".$vetCgm[numCgm]."', '".$vetCgm[codUfCorresp]."', '".$vetCgm['codMunicipioCorresp']."', '".$vetCgm['bairroCorresp']."', '".$vetCgm['logradouroCorresp']."', '".$vetCgm['cepCorresp']."' ); ";
        }
        //Verifica se o novo CGM é pessoa física ou jurídica e insere seus dados na tabela correspondente
        if ($vetCgm['pessoa'] == 'juridica' || $_REQUEST['tipo'] == 'juridica') {
            
            $dtRegistro = $vetCgm['dt_registro'] ? "TO_DATE('" . $vetCgm['dt_registro'] . "', 'DD/MM/YYYY')" : 'NULL';
            $dtRegistroCVM = $vetCgm['dt_registro_cvm'] ? "TO_DATE('" . $vetCgm['dt_registro_cvm'] . "','DD/MM/YYYY')" : 'NULL';
            $inCNPJ = $vetCgm['cnpj'] ? "'".$vetCgm['cnpj']."'" : 'NULL';
            
            $sql .= "INSERT INTO sw_cgm_pessoa_juridica(
                         numcgm,
                         cnpj,
                         insc_estadual,
                         nom_fantasia,
                         cod_orgao_registro,
                         num_registro,
                         dt_registro,
                         num_registro_cvm,
                         dt_registro_cvm,
                         objeto_social
                     )VALUES(
                         '".$vetCgm['numCgm']."',
                         ".$inCNPJ.",
                         '".$vetCgm['inscEst']."',
                         '".str_replace('\'','\'\'',$vetCgm['nomFantasia'])."',
                         " . $vetCgm['cod_orgao_registro'] . ",
                         '" . $vetCgm['num_registro'] . "',
                         " . $dtRegistro . ",
                         '" . $vetCgm['num_registro_cvm'] . "',
                         " . $dtRegistroCVM . ",
                         '" . $vetCgm['objeto_social'] . "'
                     ); ";
        } elseif ($vetCgm['pessoa'] == 'fisica' || $vetCgm['pessoa'] == 'outros') {
            $dtValidadeCnh = $vetCgm['dtValidadeCnh'] ? "TO_DATE('".$vetCgm['dtValidadeCnh']."', 'DD/MM/YYYY')": "NULL";
            $dtEmissaoRg = $vetCgm['dtEmissaoRg'] ? "TO_DATE('".$vetCgm['dtEmissaoRg']."', 'DD/MM/YYYY')": "NULL";
            $dtDataNascimento = $vetCgm['dtNascimento'] ? "to_date( '".$vetCgm['dtNascimento']."','dd/mm/yyyy')" : "NULL";
            $vetCgm['catHabilitacao'] = $vetCgm['catHabilitacao'] ? $vetCgm['catHabilitacao'] : "0";
            $vetCgm['nacionalidade'] =  $vetCgm['nacionalidade'] ? $vetCgm['nacionalidade'] : "0";
            $vetCgm['cod_escolaridade'] = $vetCgm['cod_escolaridade'] ? $vetCgm['cod_escolaridade']  : "0";
            $inCPF = $vetCgm['cpf'] ? "'".$vetCgm['cpf']."'" : 'NULL';
            $sql .= "INSERT INTO sw_cgm_pessoa_fisica(
                            numcgm,
                            cpf,
                            rg,
                            orgao_emissor,
                            cod_uf_orgao_emissor,
                            dt_emissao_rg,
                            num_cnh,
                            dt_validade_cnh,
                            cod_categoria_cnh,
                            cod_nacionalidade,
                            cod_escolaridade,
                            dt_nascimento,
                            sexo,
                            servidor_pis_pasep
                     )VALUES(
                            '$vetCgm[numCgm]',
                            $inCPF,
                            '$vetCgm[rg]',
                            '$vetCgm[orgaoEmissor]',
                            $vetCgm[inCodUFOrgaoEmissor],
                            $dtEmissaoRg,
                            '$vetCgm[numCnh]',
                            ".$dtValidadeCnh.",
                            $vetCgm[catHabilitacao],
                            $vetCgm[nacionalidade],
                            $vetCgm[cod_escolaridade],
                            $dtDataNascimento,
                            '".strtolower($vetCgm['chSexo'])."',
                            '".$_REQUEST['stPisPasep']."'
                     ); ";
        }
        
        //Chama a classe do banco de dados e executa a query
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        if ( $dataBase->executaSql($sql) ) {
            $ok = true;
        } else {
            $this->stErro = $dataBase->pegaUltimoErro();
            $ok = false;
        }
        $dataBase->fechaBD();

        return $ok;
    }//Fim da function incluiCgm

/***************************************************************************
Altera um CGM existente
Recebe os dados do CGM através de um vetor e insere na tabela do CGM
Transfere os dados atuais para a tabela de CGA
Retorna bool
/**************************************************************************/
    public function alteraCgm($altCgm)
    {
        /** Primeiro cadastra os dados atuais na tabela cad_geral_anterior, Se retornar true continua com a alteração **/
        if ( !$this->incluiCga($altCgm['numCgm'],$altCgm['codResp']) ) {
            return false;
        }

          if ($altCgm['numCgm'] && !is_null( $altCgm['codResp'] ) ) {
             $sql = "UPDATE sw_cgm
                    SET
                        cod_pais                = ".$altCgm['pais'].",
                        cod_pais_corresp        = ".$altCgm['paisCorresp'].",
                        cod_municipio           = ".$altCgm['municipio'].",
                        cod_uf                  = ".$altCgm['estado'].",
                        cod_municipio_corresp   = ".$altCgm['municipioCorresp'].",
                        cod_uf_corresp          = ".$altCgm['estadoCorresp'].",
                        nom_cgm                 = '".str_replace('\'','\'\'',$altCgm['nomCgm'])."',
                        tipo_logradouro         = '".$altCgm['stNomeTipoLogradouro']."',
                        tipo_logradouro_corresp = '".$altCgm['stNomeTipoLogradouroCorresp']."',
                        logradouro              = '".$altCgm['stNomeLogradouro']."',
                        numero                  = '".$altCgm['numero']."',
                        complemento             = '".$altCgm['complemento']."',
                        bairro                  = '".$altCgm['stNomeBairro']."',
                        cep                     = '".$altCgm['cep']."',
                        logradouro_corresp      = '".$altCgm['stNomeLogradouroCorresp']."',
                        numero_corresp          = '".$altCgm['numeroCorresp']."',
                        complemento_corresp     = '".$altCgm['complementoCorresp']."',
                        bairro_corresp          = '".$altCgm['stNomeBairroCorresp']."',
                        cep_corresp             = '".$altCgm['cepCorresp']."',
                        fone_residencial        = '".$altCgm['foneRes']."',
                        ramal_residencial       = '".$altCgm['ramalRes']."',
                        fone_comercial          = '".$altCgm['foneCom']."',
                        ramal_comercial         = '".$altCgm['ramalCom']."',
                        fone_celular            = '".$altCgm['foneCel']."',
                        e_mail                  = '".$altCgm['email']."',
                        e_mail_adcional         = '".$altCgm['emailAdic']."',
                        cod_responsavel         = ".$altCgm['codResp'].",
                        site                    = '".$altCgm['site']."'
                    WHERE numcgm = ".$altCgm['numCgm']."; \n";

           $sql .= $this->montaExcluiAtributos( $altCgm['numCgm'] )."; \n";
           $sql .= $this->montaIncluiAtributos( $altCgm['numCgm'] );

            if ( $altCgm['boCgmLogradouro'] ){
                $sql .= "UPDATE sw_cgm_logradouro 
                         SET  cod_uf         = '".$altCgm['codUf']."'
                            , cod_municipio  = '".$altCgm['codMunicipio']."'
                            , cod_bairro     = '".$altCgm['bairro']."'
                            , cod_logradouro = '".$altCgm['logradouro']."'
                            , cep            = '".$altCgm['cep']."' 
                        WHERE NUMCGM = '".$altCgm['numCgm']."'; \n";
            }else{
                $sql .= "INSERT INTO sw_cgm_logradouro ( numcgm, cod_uf, cod_municipio, cod_bairro, cod_logradouro, cep ) 
                         VALUES('".$altCgm['numCgm']."'
                                ,'".$altCgm['codUf']."'
                                ,'".$altCgm['codMunicipio']."'
                                ,'".$altCgm['bairro']."'
                                ,'".$altCgm['logradouro']."'
                                ,'".$altCgm['cep']."' 
                                ); \n";
            }
            if ( ( $altCgm['logradouroCorresp'] != 0 ) && ($altCgm['bairroCorresp'] != 0) && ($altCgm['cepCorresp'] != 0) && $altCgm['boCgmLogradouroCorresp'] ) {
                $sql .= "UPDATE sw_cgm_logradouro_correspondencia 
                         SET  cod_uf         = '".$altCgm['codUfCorresp']."'
                            , cod_municipio  = '".$altCgm['codMunicipioCorresp']."'
                            , cod_bairro     = '".$altCgm['bairroCorresp']."'
                            , cod_logradouro = '".$altCgm['logradouroCorresp']."'
                            , cep            = '".$altCgm['cepCorresp']."' 
                         WHERE NUMCGM = '".$altCgm['numCgm']."'; \n";
            } else {
                $sql .= "DELETE FROM sw_cgm_logradouro_correspondencia WHERE numcgm = '".$altCgm['numCgm']."'; \n";
                if (( $altCgm['logradouroCorresp'] != 0 ) && ($altCgm['bairroCorresp'] != 0) && ($altCgm['cepCorresp'] != 0))
                    $sql .= "INSERT INTO sw_cgm_logradouro_correspondencia ( numcgm, cod_uf, cod_municipio, cod_bairro, cod_logradouro, cep ) 
                             VALUES('".$altCgm['numCgm']."'
                                    ,'".$altCgm['codUfCorresp']."'
                                    ,'".$altCgm['codMunicipioCorresp']."'
                                    ,'".$altCgm['bairroCorresp']."'
                                    ,'".$altCgm['logradouroCorresp']."'
                                    ,'".$altCgm['cepCorresp']."' 
                                    ); \n";
            }

            /**Verifica se o novo CGM é pessoa física ou jurídica e insere seus dados na tabela correspondente **/
            if ($altCgm['stTipoAlteracao'] == 'alteracao') {
                $dataBase = new dataBaseLegado;
                $dataBase->abreBD();
                $dataBase->abreSelecao("SELECT nom_cgm FROM sw_cgm WHERE numcgm = ".$altCgm['numCgm'] );
                $dataBase->vaiPrimeiro();
                $stNomCGM = str_replace('\'','\'\'',$dataBase->pegaCampo("nom_cgm"));
                $dataBase->limpaSelecao();
                $dataBase->fechaBD();
                $sql .= "INSERT INTO cgm.cgm_alteracao_historico (numcgm, coluna,valor) VALUES ( ".$altCgm['numCgm'].",'nomcgm','".$stNomCGM."' ); \n";
            }
            if ($altCgm['pessoa'] == 'juridica' || $_REQUEST['tipo'] == 'juridica') {
                $altCgm['cnpj'] = $altCgm['cnpj'] ? "'".$altCgm['cnpj']."'" : 'NULL';
                if ( $this->isPessoaJuridica( $altCgm['numCgm'] ) ) {

                    $sql .= " UPDATE sw_cgm_pessoa_juridica
                              SET cnpj               = ".$altCgm['cnpj']."
                                , insc_estadual      = '".$altCgm['inscEst']."'
                                , nom_fantasia       = '".str_replace('\'','\'\'',$altCgm['nomFantasia'])."'
                                , cod_orgao_registro = ".$altCgm['cod_orgao_registro']."
                                , num_registro       = '".$altCgm['num_registro']."'
                                , num_registro_cvm   = '".$altCgm['num_registro_cvm']."'
                                , objeto_social      = '".$altCgm['objeto_social']."'";
                                
                    if($altCgm['dt_registro'] != '') {
                        $sql .= "   , dt_registro = TO_DATE('".$altCgm['dt_registro']."', 'DD/MM/YYYY') ";
                    } else {
                        $sql .= "   , dt_registro = NULL";
                    }
                    
                    if($altCgm['dt_registro_cvm'] != '') {
                        $sql .= "   , dt_registro_cvm = TO_DATE('".$altCgm['dt_registro_cvm']."', 'DD/MM/YYYY') ";
                    } else {
                        $sql .= "   , dt_registro_cvm = NULL";
                    }
                    
                    $sql .= " WHERE numcgm = '".$altCgm['numCgm']."'; \n";
                    
                } else {
                    $sql .= "INSERT INTO sw_cgm_pessoa_juridica (numcgm, cnpj, insc_estadual
                                                                , nom_fantasia, cod_orgao_registro, num_registro
                                                                , num_registro_cvm, objeto_social, dt_registro, dt_registro_cvm )
                             VALUES( '".$altCgm['numCgm']."'
                                    ,".$altCgm['cnpj']."
                                    ,'".$altCgm['inscEst']."'
                                    ,'".str_replace('\'','\'\'',$altCgm['nomFantasia'])."'
                                    ,".$altCgm['cod_orgao_registro']."
                                    ,'".$altCgm['num_registro']."'
                                    ,TO_DATE('".$altCgm['dt_registro']."', 'DD/MM/YYYY')
                                    ,'".$altCgm['num_registro_cvm']."'
                                    ,TO_DATE('".$altCgm['dt_registro_cvm']."', 'DD/MM/YYYY')
                                    ,'".$altCgm['objeto_social']."'";
                                    
                    if($altCgm['dt_registro'] != '') {
                        $sql .= ", TO_DATE('".$altCgm['dt_registro']."', 'DD/MM/YYYY')";
                    } else {
                        $sql .= ", NULL ";
                    }
                    
                    if($altCgm['dt_registro_cvm'] != '') {
                        $sql .= ", TO_DATE('".$altCgm['dt_registro_cvm']."', 'DD/MM/YYYY')";
                    } else {
                        $sql .= ", NULL";
                    }
                                    
                    $sql .= "); \n";
                }
                
            } elseif ($altCgm['pessoa'] == 'fisica' || $altCgm['pessoa'] == 'outros') {

                $dtValidadeCnh              = $altCgm['dtValidadeCnh']    ? "TO_DATE('".$altCgm['dtValidadeCnh']."', 'DD/MM/YYYY')": "NULL";
                $dtEmissaoRg                = $altCgm['dtEmissaoRg']      ? "TO_DATE('".$altCgm['dtEmissaoRg']."'  , 'DD/MM/YYYY')": "NULL";
                $dtDataNascimento           = $altCgm['dtNascimento']     ? "to_date('".$altCgm['dtNascimento']."' , 'DD/MM/YYYY')": "NULL";
                $vetCgm['catHabilitacao']   = $altCgm['catHabilitacao']   ? $altCgm['catHabilitacao']   : "0";
                $vetCgm['nacionalidade']    = $altCgm['nacionalidade']    ? $altCgm['nacionalidade']    : "0";
                $vetCgm['cod_escolaridade'] = $altCgm['cod_escolaridade'] ? $altCgm['cod_escolaridade'] : "0";
                $inCPF = $altCgm['cpf'] ? "'".$altCgm['cpf']."'" : 'NULL';
                if ( $this->isPessoaFisica( $altCgm['numCgm'] ) ) {

                    $sql .= "UPDATE sw_cgm_pessoa_fisica
                            SET   cpf = ".$inCPF."
                                , rg = '".$altCgm['rg']."'
                                , orgao_emissor = '".$altCgm['orgaoEmissor']."'
                                , cod_uf_orgao_emissor = ".$altCgm['inCodUFOrgaoEmissor']."
                                , dt_emissao_rg = ".$dtEmissaoRg."
                                , num_cnh = '".$altCgm['numCnh']."'
                                , dt_validade_cnh = ".$dtValidadeCnh."
                                , cod_categoria_cnh = '".$altCgm['catHabilitacao']."'
                                , cod_nacionalidade = '".$altCgm['nacionalidade']."'
                                , cod_escolaridade  = '".$altCgm['cod_escolaridade']."'
                                , dt_nascimento = ".$dtDataNascimento."
                                , sexo = '".$altCgm['chSexo']."'
                                , servidor_pis_pasep = '".$_REQUEST['stPisPasep']."'
                            WHERE numcgm = '".$altCgm['numCgm']."'; \n";
                } else {

                    $sql .= "INSERT INTO sw_cgm_pessoa_fisica (numcgm, cpf, rg, orgao_emissor,cod_uf_orgao_emissor, 
                                                                dt_emissao_rg, num_cnh, dt_validade_cnh,
                                                                cod_nacionalidade, cod_categoria_cnh, cod_escolaridade)
                            VALUES('".$altCgm['numCgm']."'
                                    ,".  $inCPF."
                                    ,'". $altCgm['rg']."'
                                    ,'". $altCgm['orgaoEmissor']."'
                                    ,".  $altCgm['inCodUFOrgaoEmissor']."
                                    ,". $dtEmissaoRg."
                                    ,'". $altCgm['numCnh']."'
                                    ,". $dtValidadeCnh."
                                    , 1
                                    ,".  $altCgm['catHabilitacao']."
                                    ,".$altCgm['cod_escolaridade']."
                                    ); \n";
                }
           }
            //Chama a classe do banco de dados e executa a query
            $dataBase = new dataBaseLegado;
            $dataBase->abreBD();
            if ($dataBase->executaSql($sql)) {
                $ok = true;
            } else {
                $this->stErro = $dataBase->pegaUltimoErro();
                $ok = false;
            }
            $dataBase->fechaBD();
        } else {
            $ok = false;
        }

        return $ok;
    }//Fim function alteraCgm

/***************************************************************************
Conversao
Altera um CGM existente
Recebe os dados do CGM através de um vetor e insere na tabela do CGM
Transfere os dados atuais para a tabela de CGA
Retorna bool
/**************************************************************************/
    public function alteraCgmConverte($altCgm)
    {
        if ($altCgm[numCgm] && !is_null( $altCgm[codResp] ) ) {
            $sql = "Update sw_cgm
                    Set
                    cod_pais='".$altCgm[pais]."',
                    cod_pais_corresp='".$altCgm[paisCorresp]."',
                    cod_municipio='".$altCgm[municipio]."',
                    cod_uf='".$altCgm[estado]."',
                    cod_municipio_corresp='".$altCgm[municipioCorresp]."',
                    cod_uf_corresp='".$altCgm[estadoCorresp]."',
                    nom_cgm='".$altCgm[nomCgm]."',
                    tipo_logradouro='".$altCgm[tipoLogradouro]."',
                    logradouro='".$altCgm[logradouro]."',
                    numero='".$altCgm[numero]."',
                    complemento='".$altCgm[complemento]."',
                    bairro='".$altCgm[bairro]."',
                    cep='".$altCgm[cep]."',
                    tipo_logradouro_corresp='".$altCgm[tipoLogradouroCorresp]."',
                    logradouro_corresp='".$altCgm[logradouroCorresp]."',
                    numero_corresp='".$altCgm[numeroCorresp]."',
                    complemento_corresp='".$altCgm[complementoCorresp]."',
                    bairro_corresp='".$altCgm[bairroCorresp]."',
                    cep_corresp='".$altCgm[cepCorresp]."',
                    fone_residencial='".$altCgm[foneRes]."',
                    ramal_residencial='".$altCgm[ramalRes]."',
                    fone_comercial='".$altCgm[foneCom]."',
                    ramal_comercial='".$altCgm[ramalCom]."',
                    fone_celular='".$altCgm[foneCel]."',
                    e_mail='".$altCgm[email]."',
                    e_mail_adcional='".$altCgm[emailAdic]."',
                    cod_responsavel='".$altCgm[codResp]."'
                    Where numcgm = '".$altCgm[numCgm]."'; ";

           $sql .= $this->montaExcluiAtributos( $altCgm[numCgm] ).";";
           $sql .= $this->montaIncluiAtributos( $altCgm[numCgm] );
            /**Verifica se o novo CGM é pessoa física ou jurídica e insere seus dados na tabela correspondente **/
            if ($altCgm[pessoa] == 'juridica') {
               $sql .= "Insert Into sw_cgm_pessoa_juridica
                        (numcgm, cnpj, insc_estadual, nom_fantasia)
                        Values('$altCgm[numCgm]', '$altCgm[cnpj]', '$altCgm[inscEst]', '".addslashes($altCgm[nomFantasia])."'); ";
            } elseif ($altCgm[pessoa] == 'fisica') {
                $dtValidadeCnh = $vetCgm[dtValidadeCnh] ? "TO_DATE('".$vetCgm[dtValidadeCnh]."', 'DD/MM/YYYY')": "NULL";
                $dtEmissaoRg = $vetCgm[dtEmissaoRg] ? "TO_DATE('".$vetCgm[dtEmissaoRg]."', 'DD/MM/YYYY')": "NULL";
                $sql .= "Insert Into sw_cgm_pessoa_fisica
                        (numcgm, cpf, rg, orgao_emissor, dt_emissao_rg, num_cnh, dt_validade_cnh, cod_nacionalidade, cod_categoria_cnh, cod_escolaridade)
                        Values('$altCgm[numCgm]', '$altCgm[cpf]', '$altCgm[rg]', '$altCgm[orgaoEmissor]', ".$dtEmissaoRg.", '$altCgm[numCnh]', ".$dtValidadeCnh.", 1, $altCgm[catHabilitacao], $altCgm[cod_escolaridade]); ";

            }
            //Chama a classe do banco de dados e executa a query
            $dataBase = new dataBaseLegado;
            $dataBase->abreBD();

            if ($dataBase->executaSql($sql)) {
                $ok = true;
            } else {
                $this->stErro = $dataBase->pegaUltimoErro();
                $ok = false;
            }
            $dataBase->fechaBD();
        } else {
            $ok = false;
        }
        //die();
        return $ok;
    }//Fim function alteraCgm

/***************************************************************************
Inclui um novo CGA para um CGM existente
Recebe o CGM a ser alterado e o usuário que está fazendo a alteração
Insere no CGA os dados atualmente cadastrados no CGM
Retorna bool
/**************************************************************************/
    public function incluiCga($numCgm,$usrAlt)
    {
        //Pega os dados do cgm atual em um vetor
        $vetCga = $this->pegaDadosCgm($numCgm);
        //Retirar o orgao emisso caso exista algum registro de RG antigo com orgão emissor concatenado
        $vetCga[rg] = trim(preg_replace("/[a-zA-Z]|\/|-/","", $vetCga[rg]));
        $vetCga['orgaoEmissor'] = trim($vetCga['orgaoEmissor']);
        if( empty($vetCga['orgaoEmissor']) ){
            $vetCga['orgaoEmissor'] = $_REQUEST['orgaoEmissor'];
        }

        $vetCga['nomCgm'] 			  = $vetCga['nomCgm'];
        $vetCga['cod_pais'] 		  = AddSlashes($vetCga['cod_pais']);
        $vetCga['cod_paisCorresp'] 	  = AddSlashes($vetCga['cod_paisCorresp']);
        $vetCga['bairro'] 			  = AddSlashes($vetCga['bairro']);
        $vetCga['orgaoEmissor'] 	  = AddSlashes($vetCga['orgaoEmissor']);
        $vetCga['numCnh'] 			  = AddSlashes($vetCga['numCnh']);
        $vetCga['logradouro'] 		  = AddSlashes($vetCga['logradouro']);
        $vetCga['complemento'] 		  = AddSlashes($vetCga['complemento']);
        $vetCga['logradouroCorresp']  = AddSlashes($vetCga['logradouroCorresp']);
        $vetCga['complementoCorresp'] = AddSlashes($vetCga['complementoCorresp']);
        $vetCga['bairroCorresp'] 	  = AddSlashes($vetCga['bairroCorresp']);
        $vetCga['numero'] 			  = AddSlashes($vetCga['numero']);
        $vetCga['numeroCorresp'] 	  = AddSlashes($vetCga['numeroCorresp']);

        //Monta a query com os dados do vetor
        $sql = "Insert Into sw_cga (
                numcgm, cod_pais, cod_pais_corresp, cod_municipio, cod_uf, cod_municipio_corresp, cod_uf_corresp,
                nom_cgm, tipo_logradouro, logradouro, numero,  complemento, bairro, cep,
                tipo_logradouro_corresp, logradouro_corresp, numero_corresp,  complemento_corresp,
                bairro_corresp, cep_corresp, fone_residencial, ramal_residencial, fone_comercial,
                ramal_comercial, fone_celular, e_mail, e_mail_adcional, cod_responsavel, cod_responsavel_alteracao
                ) Values('".$vetCga[numCgm]."', '".$vetCga[cod_pais]."', '".$vetCga[cod_paisCorresp]."',
                '".$vetCga[codMunicipio]."', '".$vetCga[codUf]."',
                '".$vetCga[codMunicipioCorresp]."', '".$vetCga[codUfCorresp]."',
                '".str_replace('\'','\'\'',$vetCga[nomCgm])."', '".$vetCga[tipoLogradouro]."', '".$vetCga[logradouro]."',
                '".$vetCga[numero]."', '".$vetCga[complemento]."',
                '".$vetCga[bairro]."', '".$vetCga[cep]."',
                '".$vetCga[tipoLogradouroCorresp]."', '".$vetCga[logradouroCorresp]."',
                '".$vetCga[numeroCorresp]."', '".$vetCga[complementoCorresp]."',
                '".$vetCga[bairroCorresp]."', '".$vetCga[cepCorresp]."',
                '".trim($vetCga[foneRes])."', '".trim($vetCga[ramalRes])."', '".trim($vetCga[foneCom])."', '".trim($vetCga[ramalCom])."',
                '".trim($vetCga[foneCel])."', '".trim($vetCga[email])."', '".trim($vetCga[emailAdic])."', '".trim($vetCga[codResp])."', '".$usrAlt."'
                );";

            if ($vetCgm[cod_logradouro] != 0) {
                $sql .= "Insert into sw_cga_logradouro ( numcgm, cod_uf, cod_municipio, cod_bairro, cod_logradouro, cep ) Values ('".$vetCga[numCgm]."', '".$vetCga[codUf]."', '".$vetCga[codMunicipio]."', '".$vetCga[cod_bairro]."', '".$vetCga[cod_logradouro]."', '".$vetCga[cep]."' ); ";
                if ($vetCgm[cod_logradouroCorresp] != 0) {
                    $sql .= "Insert into sw_cga_logradouro_correspondencia ( numcgm, cod_uf, cod_municipio, cod_bairro, cod_logradouro, cep ) Values ('".$vetCga[numCgm]."', '".$vetCga[codUfCorresp]."', '".$vetCga[codMunicipioCorresp]."', '".$vetCga[cod_bairroCorresp]."', '".$vetCga[cod_logradouroCorresp]."', '".$vetCga[cepCorresp]."' ); ";
                }
            }

            //Verifica se o novo CGM é pessoa física ou jurídica e insere seus dados na tabela correspondente
            if ($vetCga['pessoa'] == 'juridica') {
                $sql .= "Insert Into sw_cga_pessoa_juridica
                        (numcgm, timestamp, cnpj, insc_estadual, nom_fantasia)
                        Values('".$numCgm."',
                        (Select MAX(timestamp) From sw_cga Where numcgm ='".$numCgm."' ),
                        '".$vetCga['cnpj']."', '".$vetCga['inscEst']."', '".str_replace('\'','\\\'\'',$vetCga['nomFantasia'])."'); ";
            } elseif ($vetCga['pessoa'] == 'fisica') {
                $dtValidadeCnh = $vetCga[dtValidadeCnh] != "" ? "TO_DATE('".$vetCga[dtValidadeCnh]."', 'DD/MM/YYYY')" : "NULL";
                $dtEmissaoRg =  $vetCga[dtEmissaoRg] != "" ? "TO_DATE('".$vetCga[dtEmissaoRg]."','DD/MM/YYYY')" : "NULL";
                $vetCga[cod_escolaridade] = $vetCga[cod_escolaridade] ? $vetCga[cod_escolaridade] : "NULL";
                $dtDataNascimento = $vetCga[dtNascimento] ? "to_date('".$vetCga[dtNascimento]."','dd/mm/yyyy')" : "NULL";
                $sql .= "Insert Into sw_cga_pessoa_fisica
                        (numcgm, timestamp, cpf, rg, orgao_emissor, dt_emissao_rg,
                        num_cnh, dt_validade_cnh, cod_categoria_cnh, cod_nacionalidade, cod_escolaridade,dt_nascimento,sexo)
                        Values('".$numCgm."',
                        (Select MAX(timestamp) From sw_cga Where numcgm ='".$numCgm."' ),
                        '".$vetCga[cpf]."', '".$vetCga[rg]."', '".$vetCga[orgaoEmissor]."',
                        $dtEmissaoRg ,'".$vetCga[numCnh]."',".$dtValidadeCnh.",
                        '".$vetCga[catHabilitacao]."', ".$vetCga[nacionalidade].", ".$vetCga[cod_escolaridade].",
                        $dtDataNascimento,'".$vetCga[chSexo]."'); ";

            }
            $arAtributos = $this->buscaAtributosCGM( $numCgm );
            if ( count( $arAtributos ) ) {
                foreach ($arAtributos as  $arAtributo) {
                    $sql .= " Insert Into sw_cga_atributo_valor ";
                    $sql .= " (cod_atributo, numcgm, timestamp, valor) values ";
                    $sql .= " ('".$arAtributo["codAtributo"]."','".$numCgm."', ";
                    $sql .= " (Select MAX(timestamp) From sw_cga Where numcgm ='".$numCgm."' ),";
                    $sql .= " '".$arAtributo["valor"]."'); ";
                }
            }

        //Chama a classe do banco de dados e executa a query
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();        
        if ($dataBase->executaSql($sql)) {
            $ok = true;
        } else {
            $this->stErro = $dataBase->pegaUltimoErro();
            $ok = false;
        }
        $dataBase->fechaBD();

        return $ok;
    }//Fim da function incluiCga

/***************************************************************************
Exclui um cadastro de cgm se este não estiver ligado a nenhuma outra tabela
/**************************************************************************/
    public function excluiCgm($numCgm)
    {
        $sql = "";
        //Verifica se é pessoa física ou jurídica e exclui o cgm da respectiva tabela
        if ($this->isPessoaFisica($numCgm)) {
            $sql .= "Delete From sw_cgm_pessoa_fisica
                    Where numcgm = '".$numCgm."'; ";
            $sql .= "Delete From sw_cga_pessoa_fisica
                    Where numcgm = '".$numCgm."'; ";
        } elseif ($this->isPessoaJuridica($numCgm)) {
            $sql .= "Delete From sw_cgm_pessoa_juridica
                    Where numcgm = '".$numCgm."'; ";
            $sql .= "Delete From sw_cga_pessoa_juridica
                    Where numcgm = '".$numCgm."'; ";
        }
        //Exclui o cgm da tabela Cgm_atributo_valor
        $sql .= "Delete From sw_cgm_atributo_valor
                Where numcgm = '".$numCgm."'; ";
        //Exclui o cgm da tabela Cga_atributo_valor
        $sql .= "Delete From sw_cga_atributo_valor
                Where numcgm = '".$numCgm."'; ";
        $sql .= "Delete From sw_cgm_logradouro
                Where numcgm = '".$numCgm."'; ";

        $sql .= "Delete From sw_cgm_logradouro_correspondencia
                Where numcgm = '".$numCgm."'; ";

        $sql .= "Delete From sw_cga_logradouro
                Where numcgm = '".$numCgm."'; ";

        $sql .= "Delete From sw_cga_logradouro_correspondencia
                Where numcgm = '".$numCgm."'; ";
        //Exclui o cgm da tabela Cga
        $sql .= "Delete From sw_cga
                Where numcgm = '".$numCgm."'; ";
        //Exclui o cgm da tabela Cgm
        $sql .= "Delete From sw_cgm
                Where numcgm = '".$numCgm."'; ";
        //Chama a classe do banco de dados e executa a query
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();

        if ($dataBase->executaSql($sql)) {
            $ok = true;
        } else {
            $this->stErro = $dataBase->pegaUltimoErro();
            $ok = false;
        }
        $dataBase->fechaBD();

        return $ok;
    }//Fim da function excluiCgm

/***************************************************************************
Recebe um número CGM e retorna todos os dados da tabela em forma de um vetor
* alterado Luiz dia -> 31/01/2007 tabela inexistente sw_cgm_logradouro
/**************************************************************************/
    public function pegaDadosCgm($numCgm)
    {
        $sql = "Select
                    cgm.*,
                    cgmlog.cod_bairro,
                    cgmlog.cod_logradouro,
                    cgmlogc.cod_bairro AS cod_bairro_corresp,
                    cgmlogc.cod_logradouro AS cod_logradouro_corresp
                From
                    sw_cgm AS cgm

                Left Join
                    sw_cgm_logradouro AS cgmlog
                On
                    cgmlog.numcgm = cgm.numcgm

                Left Join
                    sw_cgm_logradouro_correspondencia AS cgmlogc
                On
                    cgmlogc.numcgm = cgm.numcgm

                Where
                    cgm.numcgm = '".$numCgm."' ";

        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
            $this->vetCgm = array(
                'apresentar_alteracao'=>($dataBase->pegaCampo("cod_logradouro")?false:true),
                'cod_logradouro'=>$dataBase->pegaCampo("cod_logradouro"),
                'cod_bairro'=>$dataBase->pegaCampo("cod_bairro"),
                'cod_logradouro_corresp'=>$dataBase->pegaCampo("cod_logradouro_corresp"),
                'cod_bairro_corresp'=>$dataBase->pegaCampo("cod_bairro_corresp"),
                'numCgm'=>$dataBase->pegaCampo("numcgm"),
                'cod_pais'=>$dataBase->pegaCampo("cod_pais"),
                'cod_paisCorresp'=>$dataBase->pegaCampo("cod_pais_corresp"),
                'pais'=>'',
                'paisCorresp'=>'',
                'codMunicipio'=>$dataBase->pegaCampo("cod_municipio"),
                'codUf'=>$dataBase->pegaCampo("cod_uf"),
                'codMunicipioCorresp'=>$dataBase->pegaCampo("cod_municipio_corresp"),
                'codUfCorresp'=>$dataBase->pegaCampo("cod_uf_corresp"),
                'municipio'=>'',
                'estado'=>'',
                'municipioCorresp'=>'',
                'estadoCorresp'=>'',
                'codResp'=>$dataBase->pegaCampo("cod_responsavel"),
                'nomCgm'=>stripslashes(htmlspecialchars($dataBase->pegaCampo("nom_cgm"))),
                'tipoLogradouro'=>$dataBase->pegaCampo("tipo_logradouro"),
                'logradouro'=>$dataBase->pegaCampo("logradouro"),
                'numero'=>$dataBase->pegaCampo("numero"),
                'complemento'=>$dataBase->pegaCampo("complemento"),
                'endereco'=>$dataBase->pegaCampo("tipo_logradouro")." ".$dataBase->pegaCampo("logradouro")." ".$dataBase->pegaCampo("numero")." ".$dataBase->pegaCampo("complemento"),
                'bairro'=>$dataBase->pegaCampo("bairro"),
                'cep'=>$dataBase->pegaCampo("cep"),
                'tipoLogradouroCorresp'=>$dataBase->pegaCampo("tipo_logradouro_corresp"),
                'logradouroCorresp'=>$dataBase->pegaCampo("logradouro_corresp"),
                'numeroCorresp'=>$dataBase->pegaCampo("numero_corresp"),
                'complementoCorresp'=>$dataBase->pegaCampo("complemento_corresp"),
                'enderecoCorresp'=>$dataBase->pegaCampo("tipo_logradouro_corresp")." ".$dataBase->pegaCampo("logradouro_corresp")." ".$dataBase->pegaCampo("numero_corresp")." ".$dataBase->pegaCampo("complemento_corresp"),
                'bairroCorresp'=>$dataBase->pegaCampo("bairro_corresp"),
                'cepCorresp'=>$dataBase->pegaCampo("cep_corresp"),
                'foneRes'=>$dataBase->pegaCampo("fone_residencial"),
                'ramalRes'=>$dataBase->pegaCampo("ramal_residencial"),
                'foneCom'=>$dataBase->pegaCampo("fone_comercial"),
                'ramalCom'=>$dataBase->pegaCampo("ramal_comercial"),
                'foneCel'=>$dataBase->pegaCampo("fone_celular"),
                'email'=>$dataBase->pegaCampo("e_mail"),
                'emailAdic'=>$dataBase->pegaCampo("e_mail_adcional"),
                'dtCadastro'=>$dataBase->pegaCampo("dt_cadastro"),
                'pessoa'=>"geral",
                'cnpj'=>"",
                'inscEst'=>"",
                'cpf'=>"",
                'rg'=>"",
                'site'=>$dataBase->pegaCampo("site")
                );
            }

        $dataBase->limpaSelecao();
        $dataBase->fechaBD();
            //Completa os dados de pessoa física ou jurídica
            if ($this->isPessoaFisica($numCgm)) {
                if ($this->vetAux['cpf']  and  $this->vetAux['rg']  and $this->vetAux['orgaoEmissor']) {
                    $this->vetCgm['pessoa'] = "fisica";
                } else {
                    $this->vetCgm['pessoa'] = "outros";
                    $this->vetCgm['tipo'] = "fisica";
                }

                $this->vetCgm['cpf'] = $this->vetAux['cpf'];
                $this->vetCgm['rg'] = $this->vetAux['rg'];
                $this->vetCgm['orgaoEmissor'] = $this->vetAux['orgaoEmissor'];
                $this->vetCgm['inCodUFOrgaoEmissor'] = $this->vetAux['inCodUFOrgaoEmissor'];
                $this->vetCgm['dtEmissaoRg'] = $this->vetAux['dtEmissaoRg'];
                $this->vetCgm['numCnh'] = $this->vetAux['numCnh'];
                $this->vetCgm['dtValidadeCnh'] = $this->vetAux['dtValidadeCnh'];
                $this->vetCgm['catHabilitacao'] = $this->vetAux['catHabilitacao'];
                $this->vetCgm['nacionalidade'] = $this->vetAux['nacionalidade'];
                $this->vetCgm['cod_escolaridade'] = $this->vetAux['cod_escolaridade'];
                $this->vetCgm['dtNascimento'] = $this->vetAux['dtNascimento'];
                $this->vetCgm['chSexo'] = $this->vetAux['chSexo'];
                $this->vetCgm['stPisPasep'] = $this->vetAux['stPisPasep'];
            } elseif ($this->isPessoaJuridica($numCgm)) {
                if ($this->vetAux['cnpj'] and $this->vetAux['inscEst'] and $this->vetAux['nomFantasia']) {
                    $this->vetCgm['pessoa'] = "juridica";
                } else {
                     $this->vetCgm['pessoa'] = "outros";
                     $this->vetCgm['tipo'] = "juridica";
                }
                
                $this->vetCgm['cnpj'] = $this->vetAux['cnpj'];
                $this->vetCgm['inscEst'] = $this->vetAux['inscEst'];
                $this->vetCgm['nomFantasia'] = stripslashes($this->vetAux['nomFantasia']);
                $this->vetCgm['cod_orgao_registro'] = $this->vetAux['cod_orgao_registro'];
                $this->vetCgm['num_registro'] = $this->vetAux['num_registro'];
                $this->vetCgm['dt_registro'] = $this->vetAux['dt_registro'];
                $this->vetCgm['num_registro_cvm'] = $this->vetAux['num_registro_cvm'];
                $this->vetCgm['dt_registro_cvm'] = $this->vetAux['dt_registro_cvm'];
                $this->vetCgm['objeto_social'] = $this->vetAux['objeto_social'];
            } else {
                $this->vetCgm['pessoa'] = "interno";
            }

        //Pegar os nomes dos municípios e uf's
$sql = "Select
          M.nom_municipio,
          M.nom_municipio as municipio,
          U.nom_uf,
          U.nom_uf as estado,
          P.nom_pais,
          P.nom_pais as pais
        From
          sw_municipio as M,
          sw_uf as U,
          sw_cgm as C,
          sw_pais as P
        Where
          C.cod_uf = U.cod_uf
          And C.cod_uf = M.cod_uf
          And C.cod_municipio = M.cod_municipio
          And P.cod_pais = C.cod_pais
          And C.numcgm = '".$numCgm."'";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm['pais'] = $dataBase->pegaCampo("pais");
                $this->vetCgm['nomPais'] = $dataBase->pegaCampo("nom_pais");
                $this->vetCgm['municipio'] = $dataBase->pegaCampo("municipio");
                $this->vetCgm['nomMunicipio'] = $dataBase->pegaCampo("nom_municipio");
                $this->vetCgm['estado'] = $dataBase->pegaCampo("estado");
                $this->vetCgm['nomUf'] = $dataBase->pegaCampo("nom_uf");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        //Pegar os nomes dos municípios e uf's de correspondência
$sql = "Select
          M.nom_municipio,
          M.nom_municipio as municipio,
          U.nom_uf,
          U.nom_uf as estado,
          P.nom_pais,
          P.nom_pais as paisCorresp
        From
          sw_municipio as M,
          sw_uf as U,
          sw_cgm as C,
          sw_pais as P
        Where
          C.cod_uf_corresp = U.cod_uf
          And C.cod_uf_corresp = M.cod_uf
          And C.cod_municipio_corresp = M.cod_municipio
          And P.cod_pais = C.cod_pais_corresp
          And C.numcgm = '".$numCgm."'";

        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm['nomPaisCorresp'] = $dataBase->pegaCampo("nom_pais");
                $this->vetCgm['paisCorresp'] = $dataBase->pegaCampo("paisCorresp");
                $this->vetCgm['nomMunicipioCorresp'] = $dataBase->pegaCampo("nom_municipio");
                $this->vetCgm['municipioCorresp'] = $dataBase->pegaCampo("municipio");
                $this->vetCgm['nomUfCorresp'] = $dataBase->pegaCampo("nom_uf");
                $this->vetCgm['estadoCorresp'] = $dataBase->pegaCampo("estado");
            }

        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        $sql  = "SELECT NUMCGM, USERNAME FROM administracao.usuario";
        $sql .= " WHERE NUMCGM = ".$this->vetCgm['codResp'];
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm['nomResp'] = $dataBase->pegaCampo("username");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $this->vetCgm;
    } //Fim function pegaDadosCgm

/***************************************************************************
Recebe um número CGM (conversao) e retorna todos os dados da tabela em forma de um vetor
/**************************************************************************/
    public function pegaDadosCgmConverte($numCgm,$pessoa)
    {
        $sql = "Select numcgm, cod_pais, cod_pais_corresp, cod_municipio, cod_uf, cod_municipio_corresp, cod_uf_corresp,
                cod_responsavel, nom_cgm, tipo_logradouro, logradouro, numero, complemento, bairro, cep,
                tipo_logradouro_corresp, logradouro_corresp, numero_corresp,
                complemento_corresp, bairro_corresp, cep_corresp,
                fone_residencial, ramal_residencial, fone_comercial, ramal_comercial, fone_celular, e_mail,
                e_mail_adcional, dt_cadastro
                From sw_cgm
                Where numcgm = '".$numCgm."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
            $this->vetCgm = array(
                numCgm=>$dataBase->pegaCampo("numcgm"),
                codpais=>$dataBase->pegaCampo("cod_pais"),
                codpaisCorresp=>$dataBase->pegaCampo("cod_pais_corresp"),
                codMunicipio=>$dataBase->pegaCampo("cod_municipio"),
                codUf=>$dataBase->pegaCampo("cod_uf"),
                codMunicipioCorresp=>$dataBase->pegaCampo("cod_municipio_corresp"),
                codUfCorresp=>$dataBase->pegaCampo("cod_uf_corresp"),
                pais=>'',
                paisCorresp=>'',
                municipio=>'',
                estado=>'',
                municipioCorresp=>'',
                estadoCorresp=>'',
                codResp=>$dataBase->pegaCampo("cod_responsavel"),
                nomCgm=>$dataBase->pegaCampo("nom_cgm"),
                tipoLogradouro=>$dataBase->pegaCampo("tipo_logradouro"),
                logradouro=>$dataBase->pegaCampo("logradouro"),
                numero=>$dataBase->pegaCampo("numero"),
                complemento=>$dataBase->pegaCampo("complemento"),
                endereco=>$dataBase->pegaCampo("tipo_logradouro")." ".$dataBase->pegaCampo("logradouro")." ".$dataBase->pegaCampo("numero")." ".$dataBase->pegaCampo("complemento"),
                bairro=>$dataBase->pegaCampo("bairro"),
                cep=>$dataBase->pegaCampo("cep"),
                tipoLogradouroCorresp=>$dataBase->pegaCampo("tipo_logradouro_corresp"),
                logradouroCorresp=>$dataBase->pegaCampo("logradouro_corresp"),
                numeroCorresp=>$dataBase->pegaCampo("numero_corresp"),
                complementoCorresp=>$dataBase->pegaCampo("complemento_corresp"),
                enderecoCorresp=>$dataBase->pegaCampo("tipo_logradouro_corresp")." ".$dataBase->pegaCampo("logradouro_corresp")." ".$dataBase->pegaCampo("numero_corresp")." ".$dataBase->pegaCampo("complemento_corresp"),
                bairroCorresp=>$dataBase->pegaCampo("bairro_corresp"),
                cepCorresp=>$dataBase->pegaCampo("cep_corresp"),
                foneRes=>$dataBase->pegaCampo("fone_residencial"),
                ramalRes=>$dataBase->pegaCampo("ramal_residencial"),
                foneCom=>$dataBase->pegaCampo("fone_comercial"),
                ramalCom=>$dataBase->pegaCampo("ramal_comercial"),
                foneCel=>$dataBase->pegaCampo("fone_celular"),
                email=>$dataBase->pegaCampo("e_mail"),
                emailAdic=>$dataBase->pegaCampo("e_mail_adcional"),
                dtCadastro=>$dataBase->pegaCampo("dt_cadastro"),
                pessoa=>"geral",
                cnpj=>"",
                inscEst=>"",
                cpf=>"",
                rg=>""
                );
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();
            //Completa os dados de pessoa física ou jurídica
            //if ($this->isPessoaFisica($numCgm)) {
            if ($pessoa == 'fisica') {
                $this->vetCgm[pessoa] = "fisica";
                $this->vetCgm[cpf] = "";
                $this->vetCgm[rg] = "";
                $this->vetCgm[orgaoEmissor] = "";
                $this->vetCgm[dtEmissaoRg] = "";
                $this->vetCgm[numCnh] = "";
                $this->vetCgm[dtValidadeCnh] = "";
                $this->vetCgm[catHabilitacao] = "";
                $this->vetCgm[nacionalidade] = 0;
                $this->vetCgm[cod_escolaridade] = 0;
            } elseif ($pessoa=='juridica') {
                $this->vetCgm[pessoa] = "juridica";
                $this->vetCgm[cnpj] = "";
                $this->vetCgm[inscEst] = "";
                $this->vetCgm[nomFantasia] = "";
            }

        //Pegar os nomes dos municípios e uf's
$sql = "Select
          M.nom_municipio as municipio,
          U.nom_uf as estado,
          P.nom_pais as pais
        From
          sw_municipio as M,
          sw_uf as U,
          sw_cgm as C,
          sw_pais as P
        Where
          C.cod_uf = U.cod_uf
          And C.cod_uf = M.cod_uf
          And C.cod_municipio = M.cod_municipio
          And P.cod_pais = C.cod_pais
          And C.numcgm = '".$numCgm."'";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm[pais] = $dataBase->pegaCampo("pais");
                $this->vetCgm[municipio] = $dataBase->pegaCampo("municipio");
                $this->vetCgm[estado] = $dataBase->pegaCampo("estado");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        //Pegar os nomes dos municípios e uf's de correspondência
$sql = "Select
          M.nom_municipio as municipio,
          U.nom_uf as estado,
          P.nom_pais as paisCorresp
        From
          sw_municipio as M,
          sw_uf as U,
          sw_cgm as C,
          sw_pais as P
        Where
          C.cod_uf_corresp = U.cod_uf
          And C.cod_uf_corresp = M.cod_uf
          And C.cod_municipio_corresp = M.cod_municipio
          And P.cod_pais = C.cod_pais_corresp
          And C.numcgm = '".$numCgm."'";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm[paisCorresp] = $dataBase->pegaCampo("paisCorresp");
                $this->vetCgm[municipioCorresp] = $dataBase->pegaCampo("municipio");
                $this->vetCgm[estadoCorresp] = $dataBase->pegaCampo("estado");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        $sql  = "SELECT NUMCGM, USERNAME FROM administracao.usuario";
        $sql .= " WHERE NUMCGM = ".$this->vetCgm['codResp'];
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm[nomResp] = $dataBase->pegaCampo("username");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $this->vetCgm;
    } //Fim function pegaDadosCgm

/***************************************************************************
Recebe um número CGM e diz se está cadastrado como pessoa física
Retorna bool
/**************************************************************************/
    public function isPessoaFisica($numCgm)
    {
        $sql = "Select cpf, rg, orgao_emissor, cod_uf_orgao_emissor, num_cnh, cod_escolaridade, TO_CHAR(dt_validade_cnh,'DD/MM/YYYY') AS dt_validade_cnh, cod_categoria_cnh, TO_CHAR(dt_emissao_rg,'DD/MM/YYYY') AS dt_emissao_rg, cod_nacionalidade,to_char( dt_nascimento, 'dd/mm/yyyy') as dt_nascimento,sexo,servidor_pis_pasep ";
        $sql .= " From sw_cgm_pessoa_fisica Where numcgm = '".$numCgm."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if ($dataBase->eof()) { //O CGM não é pessoa física
                $ok = false;
                $this->vetAux = array();
            } else { //O CGM é pessoa física
                $ok = true;
                $this->vetAux[cpf]                  = $dataBase->pegaCampo("cpf");
                $this->vetAux[rg]                   = $dataBase->pegaCampo("rg");
                $this->vetAux[orgaoEmissor]         = $dataBase->pegaCampo("orgao_emissor");
                $this->vetAux[inCodUFOrgaoEmissor]  = $dataBase->pegaCampo("cod_uf_orgao_emissor");
                $this->vetAux[dtEmissaoRg]          = $dataBase->pegaCampo("dt_emissao_rg");
                $this->vetAux[numCnh]               = $dataBase->pegaCampo("num_cnh");
                $this->vetAux[dtValidadeCnh]        = $dataBase->pegaCampo("dt_validade_cnh");
                $this->vetAux[catHabilitacao]       = $dataBase->pegaCampo("cod_categoria_cnh");
                $this->vetAux[nacionalidade]        = $dataBase->pegaCampo("cod_nacionalidade");
                $this->vetAux[cod_escolaridade]     = $dataBase->pegaCampo("cod_escolaridade");
                $this->vetAux[dtNascimento]         = $dataBase->pegaCampo("dt_nascimento");
                $this->vetAux[chSexo]               = $dataBase->pegaCampo("sexo");
                $this->vetAux[stPisPasep]           = $dataBase->pegaCampo("servidor_pis_pasep");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $ok;
    } //Fim function isPessoaFisica

/***************************************************************************
Recebe um número CGM e diz se está cadastrado como pessoa juridica
Retorna bool
/**************************************************************************/
    public function isPessoaJuridica($numCgm)
    {
        $sql = "Select cnpj, insc_estadual, nom_fantasia, cod_orgao_registro, num_registro, TO_CHAR(dt_registro,'dd/mm/yyyy') AS dt_registro, num_registro_cvm,
                       TO_CHAR(dt_registro_cvm,'dd/mm/yyyy') AS dt_registro_cvm, objeto_social
                  From sw_cgm_pessoa_juridica Where numcgm = '".$numCgm."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if ($dataBase->eof()) { //O CGM não é pessoa jurídica
                $ok = false;
            } else { //O CGM é pessoa jurídica
                $ok = true;
                $this->vetAux[cnpj] = $dataBase->pegaCampo("cnpj");
                $this->vetAux[inscEst] = $dataBase->pegaCampo("insc_estadual");
                $this->vetAux[nomFantasia] = $dataBase->pegaCampo("nom_fantasia");
                $this->vetAux[cod_orgao_registro] = $dataBase->pegaCampo("cod_orgao_registro");
                $this->vetAux[num_registro] = $dataBase->pegaCampo("num_registro");
                $this->vetAux[dt_registro] = $dataBase->pegaCampo("dt_registro");
                $this->vetAux[num_registro_cvm] = $dataBase->pegaCampo("num_registro_cvm");
                $this->vetAux[dt_registro_cvm] = $dataBase->pegaCampo("dt_registro_cvm");
                $this->vetAux[objeto_social] = $dataBase->pegaCampo("objeto_social");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $ok;
    } //Fim function isPessoaJuridica

/***************************************************************************
Executa uma pesquisa de CGM's conforme o parâmetro enviado
Recebe um vetor com os parâmetros a serem pesquisados
Grava uma matriz cotendo numcgm's encontrados na variável de classe $vetPesq
Retorna bool
/**************************************************************************/
    public function pesquisaCgm($vetPesq)
    {
        $sql = $this->montaPesquisaCgm($vetPesq);
        $sql .= " Order by lower(C.nom_cgm) ";
        $this->vetPesq = "";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                //Insere o resultado da busca em uma matriz
                for ($i=0;$i<$dataBase->numeroDeLinhas;$i++) {
                    $this->vetPesq[$i] = array(
                                    numCgm=>$dataBase->pegaCampo("numcgm"),
                                    nomCgm=>$dataBase->pegaCampo("nom_cgm")
                                    );
                    $dataBase->vaiProximo();
                }
                $ok = true;
            } else {
                $ok = false;
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $ok;
    }//Fim function pesquisaCgm

    public function montaPesquisaCgm($vetPesq)
    {
        $sql = "Select C.numcgm, C.nom_cgm, '' as timestamp From sw_cgm as C ";
            if (strlen($vetPesq[cnpj]) > 0) {
                $sql .= " , sw_cgm_pessoa_juridica As J ";
            }
            if (strlen($vetPesq[cpf]) > 0 or strlen($vetPesq[rg]) > 0) {
                $sql .= " , sw_cgm_pessoa_fisica As F ";
            }
        $sql .= " Where C.numcgm > 0 ";
            if (strlen($vetPesq[numCgm]) > 0) {
                $sql .= " And C.numcgm = '$vetPesq[numCgm]' ";
            }
            if (strlen($vetPesq[nomCgm]) > 0) {
                $vetPesq[nomCgm] = str_replace ("*","%",$vetPesq[nomCgm]);
                $tipoBusca = $vetPesq[tipoBusca] ? $vetPesq[tipoBusca] : 'inicio';
                switch ($tipoBusca) {
                    case 'inicio':
                        $nomCGM = $vetPesq[nomCgm]."%";
                    break;
                    case 'final':
                        $nomCGM = "%".$vetPesq[nomCgm];
                    break;
                    case 'contem':
                         $nomCGM = "%".$vetPesq[nomCgm]."%";
                    break;
                    case 'exata':
                         $nomCGM = $vetPesq[nomCgm];
                    break;
                }
                if ($tipoBusca<>'exata') {
                   $sql .= " And lower(C.nom_cgm) ILIKE lower('".$nomCGM."') ";
                } else {
                   $sql .= " And C.nom_cgm = '".$nomCGM."' ";
                }
            }
            if (strlen($vetPesq[cnpj]) > 0) {
                $sql .= " And C.numcgm = J.numcgm ";
                $sql .= " And J.cnpj = '$vetPesq[cnpj]' ";
            }
            if (strlen($vetPesq[cpf]) > 0 or strlen($vetPesq[rg]) > 0) {
                $sql .= " And C.numcgm = F.numcgm ";
            }
            if (strlen($vetPesq[cpf]) > 0) {
                $sql .= " And F.cpf = '$vetPesq[cpf]' ";
            }
            if (strlen($vetPesq[rg]) > 0) {
                $sql .= " And F.rg = '$vetPesq[rg]' ";
            }
        return $sql;
    }

    // item Converter CGM
    public function montaPesquisaCgmConverteCgmInterno($vetPesq)
    {
        $sql = "Select C.numcgm, C.nom_cgm, '' as timestamp From sw_cgm as C ";
        $sql .= " left outer join sw_cgm_pessoa_fisica";
        $sql .= "   on sw_cgm_pessoa_fisica.numcgm = C.numcgm " ;

        $sql .= " left outer join sw_cgm_pessoa_juridica";
        $sql .= "   on sw_cgm_pessoa_juridica.numcgm = C.numcgm " ;

        $sql .= " Where C.numcgm > 0 " ;
        $sql .= "   and sw_cgm_pessoa_fisica.numcgm is null " ;
        $sql .= "   and sw_cgm_pessoa_juridica.numcgm is null ";
        if (strlen($vetPesq[numCgm]) > 0) {
            $sql .= " And C.numcgm = '$vetPesq[numCgm]' ";
        }

        if (strlen($vetPesq[nomCgm]) > 0) {
            $vetPesq[nomCgm] = str_replace ("*","%",$vetPesq[nomCgm]);
            $tipoBusca = $vetPesq[tipoBusca] ? $vetPesq[tipoBusca] : 'inicio';
            switch ($tipoBusca) {
                case 'inicio':
                    $nomCGM = $vetPesq[nomCgm]."%";
                break;
                case 'final':
                    $nomCGM = "%".$vetPesq[nomCgm];
                break;
                case 'contem':
                     $nomCGM = "%".$vetPesq[nomCgm]."%";
                break;
                case 'exata':
                     $nomCGM = $vetPesq[nomCgm];
                break;
            }
            if ($tipoBusca<>'exata') {
               $sql .= " And lower(C.nom_cgm) ILIKE lower('".$nomCGM."') ";
            } else {
               $sql .= " And C.nom_cgm = '".$nomCGM."' ";
            }
        }

        return $sql;
    }

/***************************************************************************
Recebe um número CGA e retorna todos os dados da tabela em forma de um vetor
/**************************************************************************/
    public function pegaDadosCga($numCgm,$timestamp)
    {
        $sql = "Select numcgm, timestamp, cod_pais, cod_pais_corresp, cod_municipio, cod_uf, cod_municipio_corresp,
                cod_uf_corresp, cod_responsavel, nom_cgm, tipo_logradouro, logradouro, numero, complemento, bairro, cep,
                tipo_logradouro_corresp, logradouro_corresp, numero_corresp,
                complemento_corresp, bairro_corresp, cep_corresp,
                fone_residencial, ramal_residencial, fone_comercial, ramal_comercial, fone_celular, e_mail,
                e_mail_adcional, dt_cadastro
                From sw_cga
                Where numcgm = '".$numCgm."'
                And timestamp = '".$timestamp."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
            $this->vetCga = array(
                numCgm=>$dataBase->pegaCampo("numcgm"),
                timestamp=>$dataBase->pegaCampo("timestamp"),
                pais=>$dataBase->pegaCampo("cod_pais"),
                paisCorresp=>$dataBase->pegaCampo("cod_pais_corresp"),
                codMunicipio=>$dataBase->pegaCampo("cod_municipio"),
                codUf=>$dataBase->pegaCampo("cod_uf"),
                codMunicipioCorresp=>$dataBase->pegaCampo("cod_municipio_corresp"),
                codUfCorresp=>$dataBase->pegaCampo("cod_uf_corresp"),
                municipio=>'',
                estado=>'',
                municipioCorresp=>'',
                estadoCorresp=>'',
                codResp=>$dataBase->pegaCampo("cod_responsavel"),
                nomCgm=>$dataBase->pegaCampo("nom_cgm"),
                tipoLogradouro=>$dataBase->pegaCampo("tipo_logradouro"),
                logradouro=>$dataBase->pegaCampo("logradouro"),
                numero=>$dataBase->pegaCampo("numero"),
                complemento=>$dataBase->pegaCampo("complemento"),
                bairro=>$dataBase->pegaCampo("bairro"),
                endereco=>$dataBase->pegaCampo("tipo_logradouro")." ".$dataBase->pegaCampo("logradouro")." ".$dataBase->pegaCampo("numero")." ".$dataBase->pegaCampo("complemento"),
                cep=>$dataBase->pegaCampo("cep"),
                tipoLogradouroCorresp=>$dataBase->pegaCampo("tipo_logradouro_corresp"),
                logradouroCorresp=>$dataBase->pegaCampo("logradouro_corresp"),
                numeroCorresp=>$dataBase->pegaCampo("numero_corresp"),
                complementoCorresp=>$dataBase->pegaCampo("complemento_corresp"),
                bairroCorresp=>$dataBase->pegaCampo("bairro_corresp"),
                cepCorresp=>$dataBase->pegaCampo("cep_corresp"),
                enderecoCorresp=>$dataBase->pegaCampo("tipo_logradouro_corresp")." ".$dataBase->pegaCampo("logradouro_corresp")." ".$dataBase->pegaCampo("numero_corresp")." ".$dataBase->pegaCampo("complemento_corresp"),
                foneRes=>$dataBase->pegaCampo("fone_residencial"),
                ramalRes=>$dataBase->pegaCampo("ramal_residencial"),
                foneCom=>$dataBase->pegaCampo("fone_comercial"),
                ramalCom=>$dataBase->pegaCampo("ramal_comercial"),
                foneCel=>$dataBase->pegaCampo("fone_celular"),
                email=>$dataBase->pegaCampo("e_mail"),
                emailAdic=>$dataBase->pegaCampo("e_mail_adcional"),
                dtCadastro=>$dataBase->pegaCampo("dt_cadastro"),
                pessoa=>"",
                cnpj=>"",
                inscEst=>"",
                cpf=>"",
                rg=>""
                );
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();
            //Completa os dados de pessoa física ou jurídica
            if ($this->isPessoaFisicaCga($numCgm,$timestamp)) {
                $this->vetCga[pessoa] = "fisica";
                $this->vetCga[cpf] = $this->vetAux[cpf];
                $this->vetCga[rg] = $this->vetAux[rg];
                $this->vetCga[orgaoEmissor] = $this->vetAux[orgaoEmissor];
                $this->vetCga[nacionalidade] = $this->vetAux[nacionalidade];
                $this->vetCga[cod_escolaridade] = $this->vetAux[cod_escolaridade];
            } elseif ($this->isPessoaJuridicaCga($numCgm,$timestamp)) {
                $this->vetCga[pessoa] = "juridica";
                $this->vetCga[cnpj] = $this->vetAux[cnpj];
                $this->vetCga[inscEst] = $this->vetAux[inscEst];
                $this->vetCga[nomFantasia] = $this->vetAux[nomFantasia];
            }

        //Pegar os nomes dos municípios e uf's
$sql = "Select
          M.nom_municipio as municipio,
          U.nom_uf as estado,
          P.nom_pais as pais
        From
          sw_municipio as M,
          sw_uf as U,
          sw_cga as C,
          sw_pais as P
        Where
          C.cod_uf = U.cod_uf
          And C.cod_uf = M.cod_uf
          And C.cod_municipio = M.cod_municipio
          And P.cod_pais = C.cod_pais
          And C.numcgm = '".$numCgm."'
          And C.timestamp = '".$timestamp."'";

        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm[pais] = $dataBase->pegaCampo("pais");
                $this->vetCga[municipio] = $dataBase->pegaCampo("municipio");
                $this->vetCga[estado] = $dataBase->pegaCampo("estado");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        //Pegar os nomes dos municípios e uf's de correspondência
$sql = "Select
          M.nom_municipio as municipio,
          U.nom_uf as estado,
          P.nom_pais as paisCorresp
        From
          sw_municipio as M,
          sw_uf as U,
          sw_cga as C,
          sw_pais as P
        Where
          C.cod_uf_corresp = U.cod_uf
          And C.cod_uf_corresp = M.cod_uf
          And C.cod_municipio_corresp = M.cod_municipio
          And P.cod_pais = C.cod_pais_corresp
          And C.numcgm = '".$numCgm."'
          And C.timestamp = '".$timestamp."'";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCgm[paisCorresp] = $dataBase->pegaCampo("paisCorresp");
                $this->vetCga[municipioCorresp] = $dataBase->pegaCampo("municipio");
                $this->vetCga[estadoCorresp] = $dataBase->pegaCampo("estado");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        $sql  = "SELECT NUMCGM, USERNAME FROM administracao.usuario";
        $sql .= " WHERE NUMCGM = ".$this->vetCga['codResp'];
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                $this->vetCga[nomResp] = $dataBase->pegaCampo("username");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $this->vetCga;
    } //Fim function pegaDadosCga

/***************************************************************************
Recebe um número CGA e diz se está cadastrado como pessoa física
Retorna bool
/**************************************************************************/
    public function isPessoaFisicaCga($numCgm,$timestamp)
    {
        $sql = "Select numcgm, cpf, rg,dt_emissao_rg,orgao_emissor, num_cnh, cod_escolaridade, dt_validade_cnh,cod_categoria_cnh, cod_nacionalidade, cod_escolaridade  From sw_cga_pessoa_fisica
                Where numcgm = '".$numCgm."'
                And timestamp = '".$timestamp."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if ($dataBase->eof()) { //O CGA não é pessoa física
                $ok = false;
            } else { //O CGA é pessoa física
                $ok = true;
                $this->vetAux[cpf] = $dataBase->pegaCampo("cpf");
                $this->vetAux[rg] = $dataBase->pegaCampo("rg");
                $this->vetAux[orgaoEmissor] = $dataBase->pegaCampo("orgao_emissor");
                $this->vetCga[dtEmissaoRg] = $dataBase->pegaCampo("dt_emissao_rg");
                $this->vetCga[numCnh] = $dataBase->pegaCampo("num_cnh");
                $this->vetCga[catHabilitacao] = $dataBase->pegaCampo("cod_categoria_cnh");
                $this->vetCga[dtValidadeCnh] = $dataBase->pegaCampo("dt_validade_cnh");
                $this->vetCga[nacionalidade] = $dataBase->pegaCampo("cod_nacionalidade");
            //    $this->vetCga[cod_escolaridade] = $database->pegaCampo("cod_escolaridade");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $ok;
    } //Fim function isPessoaFisicaCga

/***************************************************************************
Recebe um número CGA e diz se está cadastrado como pessoa juridica
Retorna bool
/**************************************************************************/
    public function isPessoaJuridicaCga($numCgm,$timestamp)
    {
        $sql = "Select numcgm, cnpj, insc_estadual, nom_fantasia From sw_cga_pessoa_juridica
                Where numcgm = '".$numCgm."'
                And timestamp = '".$timestamp."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if ($dataBase->eof()) { //O CGA não é pessoa jurídica
                $ok = false;
            } else { //O CGA é pessoa jurídica
                $ok = true;
                $this->vetAux[cnpj] = $dataBase->pegaCampo("cnpj");
                $this->vetAux[inscEst] = $dataBase->pegaCampo("insc_estadual");
                $this->vetAux[nomFantasia] = $dataBase->pegaCampo("nom_fantasia");
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $ok;
    } //Fim function isPessoaJuridicaCga

/***************************************************************************
Executa uma pesquisa de CGA's conforme o parâmetro enviado
Recebe um vetor com os parâmetros a serem pesquisados
Grava uma matriz cotendo numcgm's encontrados na variável de classe $vetPesq
Retorna bool
/**************************************************************************/
    public function pesquisaCga($vetPesq)
    {
        $sql = $this->montaPesquisaCGA($vetPesq);
        $sql .= " Order by lower(c.nom_cgm) ASC, C.timestamp DESC ";
        $this->vetPesq = "";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            if (!$dataBase->eof()) {
                //Insere o resultado da busca em uma matriz
                for ($i=0;$i<$dataBase->numeroDeLinhas;$i++) {
                    $this->vetPesq[$i] = array(
                                    numCgm=>$dataBase->pegaCampo("numcgm"),
                                    timestamp=>$dataBase->pegaCampo("timestamp"),
                                    nomCgm=>$dataBase->pegaCampo("nom_cgm")
                                    );
                    $dataBase->vaiProximo();
                }
                $ok = true;
            } else {
                $ok = false;
            }
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $ok;
    }//Fim function pesquisaCga

    public function montaPesquisaCGA($vetPesq)
    {
        $sql = "Select C.numcgm, C.timestamp, C.nom_cgm From sw_cga as C ";
            if (strlen($vetPesq[cnpj]) > 0) {
                $sql .= " , sw_cgm_pessoa_juridica As J ";
            }
            if (strlen($vetPesq[cpf]) > 0 or strlen($vetPesq[rg]) > 0) {
                $sql .= " , sw_cgm_pessoa_fisica As F ";
            }
            $sql .= " Where C.numcgm > 0 ";
            if (strlen($vetPesq[numCgm]) > 0) {
                $sql .= " And C.numcgm = '$vetPesq[numCgm]' ";
            }
            if (strlen($vetPesq[nomCgm]) > 0) {
                $vetPesq[nomCgm] = str_replace ("*","%",$vetPesq[nomCgm]);
                $sql .= " And lower(C.nom_cgm) ILIKE lower('$vetPesq[nomCgm]%') ";
            }
/*			if (strlen($vetPesq[nomCgm]) > 0) {
                $sql .= " And lower(C.nom_cgm) Like lower('%$vetPesq[nomCgm]%') ";
            }*/
            if (strlen($vetPesq[cnpj]) > 0) {
                $sql .= " And C.numcgm = J.numcgm ";
                $sql .= " And J.cnpj = '$vetPesq[cnpj]' ";
            }
            if (strlen($vetPesq[cpf]) > 0 or strlen($vetPesq[rg]) > 0) {
                $sql .= " And C.numcgm = F.numcgm ";
            }
            if (strlen($vetPesq[cpf]) > 0) {
                $sql .= " And F.cpf = '$vetPesq[cpf]' ";
            }
            if (strlen($vetPesq[rg]) > 0) {
                $sql .= " And F.rg = '$vetPesq[rg]' ";
            }

        return $sql;
    }
/***************************************************************************
Pega as variáveis para pesquisa
/**************************************************************************/
    public function setaVariaveisBox($tipoBusca="", $numCgm="", $nomCgm="", $cpf="", $rg="", $cnpj="", $inscricaoEstadual="")
    {
        $this->tipoBusca         = $tipoBusca;
        $this->numCgm            = $numCgm;
        $this->nomCgm            = $nomCgm;
        $this->cpf               = $cpf;
        $this->rg                = $rg;
        $this->cnpj              = $cnpj;
        $this->inscricaoEstadual = $inscricaoEstadual;
    }
/***************************************************************************
Faz a pesquisa de CGM por numCgm
/**************************************************************************/
function buscaCgmBox()
{
        switch ($this->tipoBusca) {
        case "geral":
        $sSQL = "SELECT nom_cgm, numcgm FROM sw_cgm WHERE numcgm = ".$this->numCgm;
        break;
        case "fisica":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM  sw_cgm_pessoa_fisica as p, sw_cgm as c WHERE p.cpf = '".$this->numCgm."' AND p.numcgm = c.numcgm";
        if ($this->cpf != "") {
            $sSQL .= " AND p.cpf = ".$this->cpf;
        }
        if ($this->rg != "") {
            $sSQL .= " AND p.rg = ".$this->rg;
        }
        break;
        case "juridica":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM sw_cgm_pessoa_juridica as p, sw_cgm as c WHERE p.cnpj = '".$this->numCgm."' AND p.numcgm = c.numcgm";
        if ($this->cnpj != "") {
            $sSQL .= " AND p.cnpj = ".$this->cnpj;
        }
        if ($inscricaoEstadual != "") {
            $sSQL .= " AND p.insc_estadual = ".$this->inscricaoEstadual;
        }
        break;
        case "funcionario":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM sw_funcionario as p, sw_cgm as c WHERE p.numcgm = '".$this->numCgm."' AND p.numcgm = c.numcgm";
        break;
        case "usuario":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM administracao.usuario as p, sw_cgm as c WHERE p.numcgm = '".$this->numCgm."' AND p.numcgm = c.numcgm";
        break;
        }
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->boxCgm = "";
        while (!$dbEmp->eof()) {
            $nomCgm  = trim($dbEmp->pegaCampo("nom_cgm"));
            $numCgm  = trim($dbEmp->pegaCampo("numcgm"));
            $dbEmp->vaiProximo();
            $this->boxCgm .= "<a href=# onClick=\"Insere('".$numCgm."','".$nomCgm."');\">".$nomCgm."</a><br>\n";
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
}
/***************************************************************************
Faz a pesquisa de CGM por  Nome
/**************************************************************************/
function buscaCgmBoxNom()
{
        switch ($this->tipoBusca) {
        case "geral":
        $sSQL = "SELECT nom_cgm, numcgm FROM sw_cgm WHERE lower(nom_cgm) ILIKE lower('%".$this->nomCgm."%') ORDER by lower(nom_cgm)";
        break;
        case "fisica":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM  sw_cgm_pessoa_fisica as p, sw_cgm as c WHERE lower(c.nom_cgm) ILIKE lower('%".$this->nomCgm."%') AND p.numcgm = c.numcgm ORDER by lower(c.nom_cgm)";
        break;
        case "juridica":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM sw_cgm_pessoa_juridica as p, sw_cgm as c WHERE lower(c.nom_cgm) ILIKE lower('%".$this->nomCgm."%') AND p.numcgm = c.numcgm ORDER by lower(c.nom_cgm)";
        //print $sSQL;
        break;
        case "funcionario":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM sw_funcionario as p, sw_cgm as c WHERE lower(c.nom_cgm) ILIKE lower('%".$this->nomCgm."%') AND p.numcgm = c.numcgm ORDER by lower(c.nom_cgm)";
        break;
        case "usuario":
        $sSQL = "SELECT p.numcgm, c.nom_cgm FROM administracao.usuario as p, sw_cgm as c WHERE lower(c.nom_cgm) ILIKE lower('%".$this->nomCgm."%') AND p.numcgm = c.numcgm ORDER by lower(c.nom_cgm)";
        break;
        }
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->boxCgm = "";
        while (!$dbEmp->eof()) {
            $nomCgm  = trim($dbEmp->pegaCampo("nom_cgm"));
            $numCgm  = trim($dbEmp->pegaCampo("numcgm"));
            $dbEmp->vaiProximo();
            $this->boxCgm .= "<a href=# onClick=\"Insere('".$numCgm."','".$nomCgm."');\">".$nomCgm."</a><br>\n";
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
}

    public function buscaAtributosCGM($inNumCGM="")
    {
        $arAtributoCGM = array();
        $sSQL  = " SELECT ";
        $sSQL .= "     AC.COD_ATRIBUTO AS COD_ATRIBUTO, ";
        $sSQL .= "     AC.NOM_ATRIBUTO AS NOM_ATRIBUTO, ";
        $sSQL .= "     AC.TIPO AS TIPO, ";
        $sSQL .= "     AC.VALOR_PADRAO AS VALOR_PADRAO, ";
        $sSQL .= "     CAV.NUMCGM AS NUMCGM, ";
        $sSQL .= "     CAV.VALOR AS VALOR ";
        $sSQL .= " FROM ";
        $sSQL .= "     sw_atributo_cgm AS AC ";
        $sSQL .= " LEFT JOIN ";
        $sSQL .= "     sw_cgm_atributo_valor AS CAV ";
        $sSQL .= " ON ";
        $sSQL .= "     AC.COD_ATRIBUTO = CAV.COD_ATRIBUTO ";
        if ($inNumCGM) {
            $sSQL .= "     AND CAV.NUMCGM = ".$inNumCGM." ";
        } else {
            $sSQL .= "     AND CAV.NUMCGM IS NULL ";
        }
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        while (!$dbEmp->eof()) {
            $arAtributoCGM[] = array(
                                    "codAtributo" => $dbEmp->pegaCampo("COD_ATRIBUTO"),
                                    "nomAtributo" => $dbEmp->pegaCampo("NOM_ATRIBUTO"),
                                    "tipo" => $dbEmp->pegaCampo("TIPO"),
                                    "valorPadrao" => $dbEmp->pegaCampo("VALOR_PADRAO"),
                                    "numCgm" => $dbEmp->pegaCampo("NUMCGM"),
                                    "valor" => $dbEmp->pegaCampo("VALOR")
                                    );
            $dbEmp->vaiProximo();
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

        return $arAtributoCGM;
    }

    public function buscaAtributosCGA($inNumCGM="", $timestamp)
    {
        $arAtributoCGM = array();
        $sSQL .= " SELECT ";
        $sSQL .= "     AC.COD_ATRIBUTO AS COD_ATRIBUTO, ";
        $sSQL .= "     AC.NOM_ATRIBUTO AS NOM_ATRIBUTO, ";
        $sSQL .= "     AC.TIPO AS TIPO, ";
        $sSQL .= "     AC.VALOR_PADRAO AS VALOR_PADRAO, ";
        $sSQL .= "     CAV.NUMCGM AS NUMCGM, ";
        $sSQL .= "     CAV.VALOR AS VALOR ";
        $sSQL .= " FROM ";
        $sSQL .= "     sw_atributo_cgm AS AC ";
        $sSQL .= " LEFT JOIN ";
        $sSQL .= "     sw_cga_atributo_valor AS CAV ";
        $sSQL .= " ON ";
        $sSQL .= "     AC.COD_ATRIBUTO = CAV.COD_ATRIBUTO ";
        if ($inNumCGM) {
            $sSQL .= "     AND CAV.NUMCGM = ".$inNumCGM." ";
            $sSQL .= "     AND CAV.timestamp = '".$timestamp."' ";
        } else {
            $sSQL .= "     AND CAV.NUMCGM IS NULL ";
        }
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        while (!$dbEmp->eof()) {
            $arAtributoCGM[] = array(
                                    "codAtributo" => $dbEmp->pegaCampo("COD_ATRIBUTO"),
                                    "nomAtributo" => $dbEmp->pegaCampo("NOM_ATRIBUTO"),
                                    "tipo" => $dbEmp->pegaCampo("TIPO"),
                                    "valorPadrao" => $dbEmp->pegaCampo("VALOR_PADRAO"),
                                    "numCgm" => $dbEmp->pegaCampo("NUMCGM"),
                                    "valor" => $dbEmp->pegaCampo("VALOR")
                                    );
            $dbEmp->vaiProximo();
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

        return $arAtributoCGM;
    }

    public function montaExcluiAtributos($inNumCGM)
    {
        $sSQL .= " DELETE FROM sw_cgm_atributo_valor ";
        $sSQL .= " WHERE NUMCGM = ".$inNumCGM." ";

        return $sSQL;
    }

    public function montaIncluiAtributos($inNumCGM)
    {
        reset($this->vetAtributo);
        $sSQL = "";
        if ( count( $this->vetAtributo ) ) {
            foreach ($this->vetAtributo as $arAtributoCGM) {
                    $sSQL .= " INSERT INTO sw_cgm_atributo_valor ";
                    $sSQL .= " (COD_ATRIBUTO, NUMCGM, VALOR)  VALUES( ";
                    $sSQL .= $arAtributoCGM['codAtributo'].",".$inNumCGM.",";
                    $sSQL .= " '".$arAtributoCGM['valor']."'); \n";
            }
        }

        return $sSQL;
    }

    public function setAtributo($arAtributo)
    {
        if ( count( $arAtributo ) ) {
            foreach ($arAtributo as $chave => $valor) {
                if ($valor == "xxx") {
                    $valor = '';
                }
                $this->vetAtributo[] = array(
                        "codAtributo" => $chave,
                        "valor" => $valor
                       );
            }
        } else {
            $this->vetAtributo[] = "";
        }
    }
}//Fim da classe cgm
?>
